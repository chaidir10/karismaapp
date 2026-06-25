<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomHoliday extends Model
{
    protected $fillable = ['date', 'name', 'source', 'is_active'];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];
}
