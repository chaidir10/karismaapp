<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamKerja extends Model
{
    // Nama tabel (opsional, default plural dari model "jam_kerjas")
    protected $table = 'jam_kerja';

    // Field yang bisa diisi mass assignment
    protected $fillable = [
        'hari',
        'jam_masuk',
        'jam_pulang',
    ];
}
