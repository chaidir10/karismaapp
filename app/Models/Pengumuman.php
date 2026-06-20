<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumumans';

    protected $fillable = [
        'judul',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'waktu',
        'isi',
        'gambar',
        'is_active',
        'sembunyikan_detail',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sembunyikan_detail' => 'boolean',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public static function jenisOptions(): array
    {
        return [
            'pengumuman' => ['label' => 'Pengumuman', 'icon' => 'fa-newspaper', 'color' => '#3b82f6', 'gradient' => ['#3b82f6','#1d4ed8']],
            'rapat' => ['label' => 'Rapat', 'icon' => 'fa-handshake', 'color' => '#8b5cf6', 'gradient' => ['#8b5cf6','#6d28d9']],
            'info' => ['label' => 'Informasi', 'icon' => 'fa-lightbulb', 'color' => '#06b6d4', 'gradient' => ['#06b6d4','#0e7490']],
            'tugas' => ['label' => 'Tugas', 'icon' => 'fa-list-check', 'color' => '#f59e0b', 'gradient' => ['#f59e0b','#d97706']],
            'kegiatan' => ['label' => 'Kegiatan', 'icon' => 'fa-calendar-day', 'color' => '#10b981', 'gradient' => ['#10b981','#059669']],
            'pengingat' => ['label' => 'Pengingat', 'icon' => 'fa-bell-concierge', 'color' => '#ec4899', 'gradient' => ['#ec4899','#be185d']],
            'lainnya' => ['label' => 'Lainnya', 'icon' => 'fa-circle-info', 'color' => '#64748b', 'gradient' => ['#64748b','#475569']],
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
