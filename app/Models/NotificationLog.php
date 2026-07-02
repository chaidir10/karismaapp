<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'title', 'body', 'url', 'tag', 'is_read', 'read_at', 'created_at'];

    protected $casts = ['is_read' => 'boolean', 'read_at' => 'datetime', 'created_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
