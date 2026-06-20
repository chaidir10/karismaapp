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
            'pengumuman' => ['label' => 'Pengumuman', 'icon' => 'fa-newspaper', 'color' => '#3b82f6'],
            'rapat' => ['label' => 'Rapat', 'icon' => 'fa-handshake', 'color' => '#8b5cf6'],
            'info' => ['label' => 'Informasi', 'icon' => 'fa-lightbulb', 'color' => '#06b6d4'],
            'tugas' => ['label' => 'Tugas', 'icon' => 'fa-list-check', 'color' => '#f59e0b'],
            'kegiatan' => ['label' => 'Kegiatan', 'icon' => 'fa-calendar-day', 'color' => '#10b981'],
            'pengingat' => ['label' => 'Pengingat', 'icon' => 'fa-bell-concierge', 'color' => '#ec4899'],
            'lainnya' => ['label' => 'Lainnya', 'icon' => 'fa-circle-info', 'color' => '#64748b'],
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
