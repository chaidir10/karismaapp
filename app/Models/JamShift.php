<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamShift extends Model
{
    use HasFactory;

    protected $table = 'jam_shift';

    protected $fillable = [
        'nama',
        'jam_masuk',
        'jam_pulang',
    ];
}
