<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\PushSubscription;

class NotificationLogger
{
    public static function log(int $userId, string $title, string $body = '', string $url = '/pegawai/dashboard', string $tag = ''): void
    {
        NotificationLog::create([
            'user_id'    => $userId,
            'title'      => $title,
            'body'       => $body,
            'url'        => $url,
            'tag'        => $tag,
            'is_read'    => false,
            'created_at' => now(),
        ]);
    }

    // Log untuk semua user yang punya subscription aktif
    public static function logToAllSubscribers(string $title, string $body = '', string $url = '/pegawai/dashboard', string $tag = ''): void
    {
        $userIds = PushSubscription::distinct()->pluck('user_id');
        $now = now();
        $rows = $userIds->map(fn($uid) => [
            'user_id'    => $uid,
            'title'      => $title,
            'body'       => $body,
            'url'        => $url,
            'tag'        => $tag,
            'is_read'    => false,
            'created_at' => $now,
        ])->toArray();

        if (!empty($rows)) {
            NotificationLog::insert($rows);
        }
    }
}
