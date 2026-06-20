<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'waktu',
        'isi',
        'gambar',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public static function jenisOptions(): array
    {
        return [
            'pengumuman' => ['label' => 'Pengumuman', 'icon' => 'fa-bullhorn', 'color' => '#3b82f6'],
            'rapat' => ['label' => 'Rapat', 'icon' => 'fa-users', 'color' => '#8b5cf6'],
            'info' => ['label' => 'Informasi', 'icon' => 'fa-info-circle', 'color' => '#06b6d4'],
            'tugas' => ['label' => 'Tugas', 'icon' => 'fa-clipboard-list', 'color' => '#f59e0b'],
            'kegiatan' => ['label' => 'Kegiatan', 'icon' => 'fa-calendar-check', 'color' => '#10b981'],
            'lainnya' => ['label' => 'Lainnya', 'icon' => 'fa-bell', 'color' => '#64748b'],
        ];
    }

    public function getIconAttribute(): string
    {
        return self::jenisOptions()[$this->jenis]['icon'] ?? 'fa-bell';
    }

    public function getColorAttribute(): string
    {
        return self::jenisOptions()[$this->jenis]['color'] ?? '#64748b';
    }
}
