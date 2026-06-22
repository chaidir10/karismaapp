<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CutiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'jenis_cuti' => 'required|in:cuti_tahunan,cuti_sakit,cuti_melahirkan,cuti_besar,cuti_alasan_penting,dinas_luar',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string|max:255',
            'bukti_surat' => 'required|file|max:2048',
        ]);

        try {
            $path = $request->file('bukti_surat')->store('bukti_cuti', 'public');

            Cuti::create([
                'user_id' => Auth::id(),
                'jenis' => $request->jenis_cuti,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'keterangan' => $request->keterangan,
                'bukti_surat' => $path,
                'status' => 'pending',
            ]);

            return redirect()->route('pegawai.pengajuan.index')
                ->with('success', 'Pengajuan cuti/DL berhasil diajukan');
        } catch (\Exception $e) {
            return redirect()->route('pegawai.pengajuan.index')
                ->with('error', 'Gagal mengajukan: ' . $e->getMessage());
        }
    }
}
