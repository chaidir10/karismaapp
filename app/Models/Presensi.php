<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Presensi extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'presensis';

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'user_id',
        'tanggal',
        'jenis',   // 'masuk' atau 'pulang'
        'jam',
        'foto',
        'lokasi',
        'status',  // 'pending', 'approved', 'rejected'
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
}
