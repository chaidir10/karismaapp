<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceIssue extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'issue_type', 'user_agent', 'reported_at', 'resolved_at'];

    protected $casts = [
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function report(int $userId, string $type, ?string $ua = null): void
    {
        $recent = static::where('user_id', $userId)
            ->where('issue_type', $type)
            ->whereNull('resolved_at')
            ->where('reported_at', '>=', now()->subHours(1))
            ->exists();

        if ($recent) return;

        static::create([
            'user_id' => $userId,
            'issue_type' => $type,
            'user_agent' => $ua ? substr($ua, 0, 500) : null,
            'reported_at' => now(),
        ]);
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            'camera_blocked' => 'Kamera Diblokir',
            'camera_error' => 'Kamera Gagal',
            'location_blocked' => 'Lokasi Diblokir',
            'location_error' => 'Lokasi Gagal',
            default => $type,
        };
    }

    public static function typeIcon(string $type): array
    {
        return match ($type) {
            'camera_blocked', 'camera_error' => ['icon' => 'fa-camera', 'color' => '#ef4444', 'bg' => 'rgba(239,68,68,0.1)'],
            'location_blocked', 'location_error' => ['icon' => 'fa-location-dot', 'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.1)'],
            default => ['icon' => 'fa-circle-exclamation', 'color' => '#64748b', 'bg' => 'rgba(100,116,139,0.1)'],
        };
    }
}
