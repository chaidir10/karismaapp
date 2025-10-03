<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanPresensi extends Model
{
    protected $table = 'pengajuan_presensi';

    protected $fillable = [
        'user_id',
        'jenis',
        'tanggal',
        'alasan',
        'bukti',
        'status',
        'approved_by',
        'approved_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
