<?php
// Jalankan di server: php artisan tinker < fix_lembur_pengajuan.php
// Script ini memindahkan presensi lembur yang tertimpa pengajuan ke reguler

use App\Models\Presensi;

$today = '2026-06-22';

// Cari presensi lembur yang fotonya dari pengajuan (bukan dari kamera)
$affected = Presensi::where('tanggal', $today)
    ->where('is_lembur', true)
    ->where(function($q) {
        $q->where('foto', 'LIKE', '%bukti_pengajuan%')
          ->orWhereNull('lokasi');
    })
    ->get();

echo "Found {$affected->count()} potentially affected records\n";

foreach ($affected as $p) {
    echo "ID:{$p->id} | User:{$p->user_id} | {$p->jenis} | Jam:{$p->jam} | Foto:{$p->foto}\n";

    // Cek apakah user punya record reguler untuk tanggal+jenis yang sama
    $reguler = Presensi::where('user_id', $p->user_id)
        ->where('tanggal', $today)
        ->where('jenis', $p->jenis)
        ->where('is_lembur', false)
        ->first();

    if (!$reguler) {
        // Buat record reguler baru dari data yang salah masuk ke lembur
        $newReguler = Presensi::create([
            'user_id' => $p->user_id,
            'tanggal' => $today,
            'jenis' => $p->jenis,
            'jam' => $p->jam,
            'foto' => $p->foto,
            'lokasi' => $p->lokasi,
            'status' => 'approved',
            'is_lembur' => false,
        ]);
        echo "  -> Created reguler record ID:{$newReguler->id}\n";
    }

    // Hapus record lembur yang salah (yang fotonya dari pengajuan)
    $p->delete();
    echo "  -> Deleted wrong lembur record ID:{$p->id}\n";
}

echo "\nDone!\n";
