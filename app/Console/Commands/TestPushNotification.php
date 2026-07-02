<?php

namespace App\Console\Commands;

use App\Models\PushSubscription;
use Illuminate\Console\Command;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class TestPushNotification extends Command
{
    protected $signature   = 'push:test {message? : Pesan yang dikirim}';
    protected $description = 'Kirim test push notification ke semua subscriber';

    public function handle(): void
    {
        $message = $this->argument('message') ?? 'Selamat bekerja dan beraktifitas!';

        $subs = PushSubscription::all();

        if ($subs->isEmpty()) {
            $this->error('Tidak ada subscriber. Buka dashboard di HP dulu dan izinkan notifikasi.');
            return;
        }

        $this->info("Mengirim ke {$subs->count()} subscriber...");

        $auth = [
            'VAPID' => [
                'subject'    => config('services.vapid.subject'),
                'publicKey'  => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ];

        $webPush = new WebPush($auth);
        $webPush->setReuseVAPIDHeaders(true);

        $payload = json_encode([
            'title' => '📢 Karisma',
            'body'  => $message,
            'tag'   => 'karisma-test-'.time(),
            'url'   => '/pegawai/dashboard',
        ]);

        foreach ($subs as $sub) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint'  => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                ]),
                $payload
            );
        }

        $sent = 0;
        $failed = 0;
        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) {
                $sent++;
            } else {
                $failed++;
                // Hapus subscription tidak valid
                if (in_array($report->getResponse()?->getStatusCode(), [404, 410])) {
                    PushSubscription::where('endpoint', $report->getEndpoint())->delete();
                    $this->warn('Subscription expired dihapus: '.$report->getEndpoint());
                } else {
                    $this->error('Gagal: '.$report->getReason());
                }
            }
        }

        $this->info("Terkirim: {$sent} | Gagal: {$failed}");
    }
}
