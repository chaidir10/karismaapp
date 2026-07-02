<?php

namespace App\Console\Commands;

use App\Models\PushSubscription;
use App\Services\WebPushSender;
use Illuminate\Console\Command;

class TestPushNotification extends Command
{
    protected $signature   = 'push:test {message? : Pesan yang dikirim}';
    protected $description = 'Kirim test push notification ke semua subscriber';

    public function handle(): void
    {
        $message = $this->argument('message') ?? 'Selamat bekerja dan beraktifitas!';
        $subs    = PushSubscription::all();

        if ($subs->isEmpty()) {
            $this->error('Tidak ada subscriber.');
            return;
        }

        $this->info("Mengirim ke {$subs->count()} subscriber...");

        $sender = new WebPushSender();
        $sent   = 0;
        $failed = 0;

        foreach ($subs as $sub) {
            $code = $sender->send(
                $sub->endpoint,
                $sub->public_key,
                $sub->auth_token,
                ['title' => '📢 Karisma', 'body' => $message, 'tag' => 'karisma-test-' . time(), 'url' => '/pegawai/dashboard']
            );

            if ($code >= 200 && $code < 300) {
                $sent++;
                $this->info("Terkirim ({$code})");
            } elseif (in_array($code, [404, 410])) {
                $sub->delete();
                $this->warn("Subscription kadaluarsa dihapus ({$code})");
                $failed++;
            } else {
                $this->error("Gagal ({$code}): " . $sub->endpoint);
                $failed++;
            }
        }

        $this->info("Selesai — Terkirim: {$sent} | Gagal: {$failed}");
    }
}
