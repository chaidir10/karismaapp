<?php

namespace App\Console\Commands;

use App\Models\PushSubscription;
use App\Models\User;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class SendAbsensiReminders extends Command
{
    protected $signature   = 'absensi:remind';
    protected $description = 'Kirim push notification reminder absensi masuk/pulang';

    public function handle(): void
    {
        $now      = Carbon::now();
        $today    = $now->toDateString();
        $holidays = \App\Helpers\HolidayHelper::get($now->year);

        // Skip hari libur & weekend
        if ($now->isWeekend() || isset($holidays[$today])) return;

        $auth = [
            'VAPID' => [
                'subject'    => config('services.vapid.subject'),
                'publicKey'  => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ];

        $webPush = new WebPush($auth);
        $webPush->setReuseVAPIDHeaders(true);

        $users = User::nonTester()
            ->whereHas('pushSubscriptions')
            ->get();

        foreach ($users as $user) {
            $jadwal     = $user->getJadwalKerja($now);
            $jamMasuk   = Carbon::createFromFormat('H:i', substr($jadwal['jam_masuk'], 0, 5));
            $jamPulang  = Carbon::createFromFormat('H:i', substr($jadwal['jam_pulang'], 0, 5));

            $sudahMasuk  = Presensi::where('user_id', $user->id)
                ->where('tanggal', $today)->where('jenis', 'masuk')
                ->where('is_lembur', false)->where('status', 'approved')->exists();
            $sudahPulang = Presensi::where('user_id', $user->id)
                ->where('tanggal', $today)->where('jenis', 'pulang')
                ->where('is_lembur', false)->where('status', 'approved')->exists();

            $messages = $this->buildMessages($now, $jamMasuk, $jamPulang, $sudahMasuk, $sudahPulang);
            if (empty($messages)) continue;

            foreach ($user->pushSubscriptions as $sub) {
                $subscription = Subscription::create([
                    'endpoint'  => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                ]);

                foreach ($messages as $msg) {
                    $webPush->queueNotification(
                        $subscription,
                        json_encode($msg)
                    );
                }
            }
        }

        foreach ($webPush->flush() as $report) {
            if (!$report->isSuccess()) {
                // Hapus subscription tidak valid (expired/unsubscribed)
                if (in_array($report->getResponse()?->getStatusCode(), [404, 410])) {
                    PushSubscription::where('endpoint', $report->getEndpoint())->delete();
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

        // 10 menit sebelum masuk (window 1 menit)
        if (!$sudahMasuk && $min >= ($mMasuk - 10) && $min < ($mMasuk - 9)) {
            $msgs[] = [
                'title' => '⏰ Reminder Presensi Masuk',
                'body'  => 'Waktu masuk kerja 10 menit lagi ('.$jamMasuk->format('H:i').'). Jangan terlambat!',
                'tag'   => 'masuk-reminder',
            ];
        }

        // Tepat jam masuk, belum absen (window 1 menit)
        if (!$sudahMasuk && $min >= $mMasuk && $min < ($mMasuk + 1)) {
            $msgs[] = [
                'title' => '🔔 Waktunya Presensi Masuk',
                'body'  => 'Jam masuk kerja sudah tiba. Segera lakukan presensi masuk!',
                'tag'   => 'masuk-now',
            ];
        }

        // 15 menit setelah jam masuk, belum absen (window 1 menit)
        if (!$sudahMasuk && $min >= ($mMasuk + 15) && $min < ($mMasuk + 16)) {
            $msgs[] = [
                'title' => '⚠️ Belum Presensi Masuk',
                'body'  => 'Anda belum melakukan presensi masuk. Segera presensi sekarang!',
                'tag'   => 'masuk-late',
            ];
        }

        // 10 menit sebelum pulang (window 1 menit)
        if ($sudahMasuk && !$sudahPulang && $min >= ($mPulang - 10) && $min < ($mPulang - 9)) {
            $msgs[] = [
                'title' => '⏰ Reminder Presensi Pulang',
                'body'  => 'Waktu pulang kerja 10 menit lagi ('.$jamPulang->format('H:i').'). Jangan lupa presensi!',
                'tag'   => 'pulang-reminder',
            ];
        }

        // Tepat jam pulang, belum absen (window 1 menit)
        if ($sudahMasuk && !$sudahPulang && $min >= $mPulang && $min < ($mPulang + 1)) {
            $msgs[] = [
                'title' => '🏠 Waktunya Pulang!',
                'body'  => 'Jam kerja selesai. Jangan lupa presensi pulang sebelum meninggalkan kantor!',
                'tag'   => 'pulang-now',
            ];
        }

        return $msgs;
    }
}
