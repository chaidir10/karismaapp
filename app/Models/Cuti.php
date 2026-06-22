<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    protected $fillable = [
        'user_id', 'jenis', 'tanggal_mulai', 'tanggal_selesai',
        'keterangan', 'bukti_surat', 'status', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'approved_at' => 'datetime',
    ];

    public static function jenisOptions()
    {
        return [
            'cuti_tahunan' => 'Cuti Tahunan',
            'cuti_sakit' => 'Cuti Sakit',
            'cuti_melahirkan' => 'Cuti Melahirkan',
            'cuti_besar' => 'Cuti Besar',
            'cuti_alasan_penting' => 'Cuti Alasan Penting',
            'dinas_luar' => 'Dinas Luar (DL)',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getJumlahHariAttribute()
    {
        $holidays = \App\Helpers\HolidayHelper::getDates($this->tanggal_mulai->year);
        $count = 0;
        for ($d = $this->tanggal_mulai->copy(); $d->lte($this->tanggal_selesai); $d->addDay()) {
            if ($d->dayOfWeek == 0 || $d->dayOfWeek == 6) continue;
            if (in_array($d->format('Y-m-d'), $holidays)) continue;
            $count++;
        }
        return $count;
    }

    public function getLabelAttribute()
    {
        return self::jenisOptions()[$this->jenis] ?? $this->jenis;
    }
}
