<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Kolom yang bisa diisi (mass assignable).
     */
    protected $fillable = [
        'nip',
        'name',
        'jabatan',
        'unit_id',
        'role',
        'can_approve_pengajuan',
        'foto_profil',
        'no_hp',
        'alamat',
        'jenis_pegawai',
        'email',
        'password',
    ];

    /**
     * Kolom yang disembunyikan.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast kolom tertentu.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'can_approve_pengajuan' => 'boolean',
        ];
    }

    /**
     * Relasi ke presensi.
     */
    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    /**
     * Relasi ke wilayah kerja.
     */
    public function wilayahKerja()
    {
        return $this->belongsTo(WilayahKerja::class, 'unit_id');
    }
}
