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

    public function users()
    {
        return $this->hasMany(User::class, 'unit_id');
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'user_wilayah_kerja', 'wilayah_kerja_id', 'user_id')->withTimestamps();
    }
}
