<?php

namespace App\Console\Commands;

use App\Models\PushSubscription;
use App\Models\User;
use App\Models\Presensi;
use App\Services\WebPushSender;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAbsensiReminders extends Command
{
    protected $signature   = 'absensi:remind';
    protected $description = 'Kirim push notification reminder absensi masuk/pulang';

    public function handle(): void
    {
        $now      = Carbon::now();
        $today    = $now->toDateString();
        $holidays = \App\Helpers\HolidayHelper::get($now->year);

        if ($now->isWeekend() || isset($holidays[$today])) return;

        $sender = new WebPushSender();

        $users = User::nonTester()
            ->whereHas('pushSubscriptions')
            ->get();

        foreach ($users as $user) {
            $jadwal    = $user->getJadwalKerja($now);
            $jamMasuk  = Carbon::createFromFormat('H:i', substr($jadwal['jam_masuk'], 0, 5));
            $jamPulang = Carbon::createFromFormat('H:i', substr($jadwal['jam_pulang'], 0, 5));

            $sudahMasuk  = Presensi::where('user_id', $user->id)
                ->where('tanggal', $today)->where('jenis', 'masuk')
                ->where('is_lembur', false)->where('status', 'approved')->exists();
            $sudahPulang = Presensi::where('user_id', $user->id)
                ->where('tanggal', $today)->where('jenis', 'pulang')
                ->where('is_lembur', false)->where('status', 'approved')->exists();

            $messages = $this->buildMessages($now, $jamMasuk, $jamPulang, $sudahMasuk, $sudahPulang);
            if (empty($messages)) continue;

            foreach ($user->pushSubscriptions as $sub) {
                foreach ($messages as $msg) {
                    $code = $sender->send($sub->endpoint, $sub->public_key, $sub->auth_token, $msg);
                    if (in_array($code, [404, 410])) {
                        $sub->delete();
                        break;
                    }
                }
            }
        }
    }

    private function buildMessages(Carbon $now, Carbon $jamMasuk, Carbon $jamPulang, bool $sudahMasuk, bool $sudahPulang): array
    {
        $msgs = [];
        $min  = $now->minute + ($now->hour * 60);

        $mMasuk  = $jamMasuk->minute  + ($jamMasuk->hour  * 60);
        $mPulang = $jamPulang->minute + ($jamPulang->hour * 60);

        // 10 menit sebelum masuk
        if (!$sudahMasuk && $min >= ($mMasuk - 10) && $min < ($mMasuk - 9)) {
            $msgs[] = ['title' => '⏰ Reminder Presensi Masuk', 'body' => 'Waktu masuk kerja 10 menit lagi ('.$jamMasuk->format('H:i').'). Jangan terlambat!', 'tag' => 'masuk-reminder', 'url' => '/pegawai/dashboard'];
        }
        // Tepat jam masuk, belum absen
        if (!$sudahMasuk && $min >= $mMasuk && $min < ($mMasuk + 1)) {
            $msgs[] = ['title' => '🔔 Waktunya Presensi Masuk', 'body' => 'Jam masuk kerja sudah tiba. Segera lakukan presensi masuk!', 'tag' => 'masuk-now', 'url' => '/pegawai/dashboard'];
        }
        // 15 menit setelah jam masuk, belum absen
        if (!$sudahMasuk && $min >= ($mMasuk + 15) && $min < ($mMasuk + 16)) {
            $msgs[] = ['title' => '⚠️ Belum Presensi Masuk', 'body' => 'Anda belum melakukan presensi masuk. Segera presensi sekarang!', 'tag' => 'masuk-late', 'url' => '/pegawai/dashboard'];
        }
        // 10 menit sebelum pulang
        if ($sudahMasuk && !$sudahPulang && $min >= ($mPulang - 10) && $min < ($mPulang - 9)) {
            $msgs[] = ['title' => '⏰ Reminder Presensi Pulang', 'body' => 'Waktu pulang kerja 10 menit lagi ('.$jamPulang->format('H:i').'). Jangan lupa presensi!', 'tag' => 'pulang-reminder', 'url' => '/pegawai/dashboard'];
        }
        // Tepat jam pulang, belum absen
        if ($sudahMasuk && !$sudahPulang && $min >= $mPulang && $min < ($mPulang + 1)) {
            $msgs[] = ['title' => '🏠 Waktunya Pulang!', 'body' => 'Jam kerja selesai. Jangan lupa presensi pulang sebelum meninggalkan kantor!', 'tag' => 'pulang-now', 'url' => '/pegawai/dashboard'];
        }

        return $msgs;
    }
}
