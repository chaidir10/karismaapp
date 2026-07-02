<?php

namespace App\Console\Commands;

use App\Models\PushSubscription;
use App\Services\WebPushSender;
use Illuminate\Console\Command;

class DiagnosePushSubscriptions extends Command
{
    protected $signature   = 'push:diagnose {--send : Kirim push test ke tiap subscriber}';
    protected $description = 'Tampilkan semua push subscription dan (opsional) test kirim';

    public function handle(): void
    {
        $subs = PushSubscription::with('user')->get();

        if ($subs->isEmpty()) {
            $this->error('Tidak ada subscription di database.');
            return;
        }

        $this->info("Total subscription: {$subs->count()}");
        $this->newLine();

        $rows = [];
        foreach ($subs as $sub) {
            $rows[] = [
                $sub->id,
                $sub->user ? $sub->user->name : '(user dihapus)',
                $sub->user_id,
                substr($sub->endpoint, 0, 50) . '...',
                $sub->created_at->format('d/m/Y H:i'),
            ];
        }

        $this->table(['ID', 'Nama', 'User ID', 'Endpoint (50 char)', 'Dibuat'], $rows);

        if (!$this->option('send')) {
            $this->newLine();
            $this->line('Tambah --send untuk test kirim ke semua subscriber.');
            return;
        }

        $this->newLine();
        $this->info('Mengirim push test...');
        $sender = new WebPushSender();

        foreach ($subs as $sub) {
            $name = $sub->user ? $sub->user->name : "user_id={$sub->user_id}";
            $code = $sender->send(
                $sub->endpoint,
                $sub->public_key,
                $sub->auth_token,
                ['title' => '🔔 Test Push', 'body' => "Halo {$name}, ini test notifikasi!", 'tag' => 'diagnose-' . time(), 'url' => '/pegawai/dashboard']
            );

            if ($code === 201) {
                $this->info("  ✓ [{$sub->id}] {$name} → Terkirim (201)");
            } elseif (in_array($code, [404, 410])) {
                $this->warn("  ✗ [{$sub->id}] {$name} → Subscription kadaluarsa ({$code}) — dihapus");
                $sub->delete();
            } else {
                $this->error("  ✗ [{$sub->id}] {$name} → Gagal ({$code}) — lihat storage/logs/laravel.log");
            }
        }
    }
}
