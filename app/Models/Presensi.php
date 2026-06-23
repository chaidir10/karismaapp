<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\JamShift;

class Presensi extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'presensis';

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'user_id',
        'tanggal',
        'jenis',
        'jam',
        'foto',
        'lokasi',
        'status',
        'is_lembur',
        'is_darurat',
        'jam_shift_id',
    ];

    protected $casts = [
        'is_lembur' => 'boolean',
        'is_darurat' => 'boolean',
    ];

    // Tanggal otomatis dikonversi ke Carbon
    protected $dates = [
        'tanggal',
        'created_at',
        'updated_at',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jamShift()
    {
        return $this->belongsTo(JamShift::class);
    }
}
