<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WilayahKerja extends Model
{
    protected $table = 'wilayah_kerja'; // Nama tabel

    protected $fillable = [
        'nama',
        'alamat',
        'telepon',
        'latitude',
        'longitude',
        'radius',
    ];

    // Relasi ke users
    public function users()
    {
        return $this->hasMany(User::class, 'unit_id');
    }
}
