<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Presensi;
use App\Models\WilayahKerja;
use App\Models\JamShift;
use App\Models\JamKerja;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
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
        'can_shift',
        'jam_shift_id',
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
            'can_shift' => 'boolean', // ✅ pastikan dibaca sebagai boolean
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
     * Relasi ke wilayah kerja (utama, backward-compatible).
     */
    public function wilayahKerja()
    {
        return $this->belongsTo(WilayahKerja::class, 'unit_id');
    }

    /**
     * Relasi many-to-many ke semua wilayah kerja yang di-assign.
     */
    public function wilayahKerjaList()
    {
        return $this->belongsToMany(WilayahKerja::class, 'user_wilayah_kerja', 'user_id', 'wilayah_kerja_id')->withTimestamps();
    }

    public function jamShift()
    {
        return $this->belongsTo(JamShift::class);
    }

    /**
     * Ambil jam masuk & jam pulang efektif untuk tanggal tertentu.
     * Shift employee → pakai jam_shift.
     * Regular employee → pakai jam_kerja sesuai hari.
     */
    public function getJadwalKerja($tanggal = null)
    {
        $tanggal = $tanggal ? \Carbon\Carbon::parse($tanggal) : now();

        $hariMap = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => "Jum'at", 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu',
        ];
        $hari = $hariMap[$tanggal->format('l')] ?? $tanggal->translatedFormat('l');

        // Cek shift dari record presensi masuk hari itu
        if ($this->can_shift) {
            $presensiMasuk = Presensi::where('user_id', $this->id)
                ->where('tanggal', $tanggal->format('Y-m-d'))
                ->where('jenis', 'masuk')
                ->where('is_lembur', false)
                ->whereNotNull('jam_shift_id')
                ->first();

            if ($presensiMasuk && $presensiMasuk->jam_shift_id) {
                $shift = JamShift::find($presensiMasuk->jam_shift_id);
                if ($shift) {
                    return [
                        'jam_masuk'  => $shift->jam_masuk,
                        'jam_pulang' => $shift->jam_pulang,
                        'is_shift'   => true,
                        'nama_shift' => $shift->nama,
                    ];
                }
            }
        }

        $jamKerja = JamKerja::where('hari', $hari)->first();
        if ($jamKerja) {
            return [
                'jam_masuk'  => $jamKerja->jam_masuk,
                'jam_pulang' => $jamKerja->jam_pulang,
                'is_shift'   => false,
                'nama_shift' => null,
            ];
        }

        return [
            'jam_masuk'  => '07:30:00',
            'jam_pulang' => '16:00:00',
            'is_shift'   => false,
            'nama_shift' => null,
        ];
    }
}
