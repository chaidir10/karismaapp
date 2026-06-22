<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengajuanPresensi;
use App\Models\Cuti;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
    public function index()
    {
        $pengajuan = PengajuanPresensi::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        try {
            $cutiList = Cuti::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            $cutiList = collect();
        }

        return view('pegawai.pengajuan', compact('pengajuan', 'cutiList'));
    }

    public function create()
    {
        return view('pegawai.pengajuan');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis'   => 'required|in:masuk,pulang',
            'tanggal' => 'required|date',
            'waktu'   => 'required|date_format:H:i',
            'alasan'  => 'required|string|max:255',
            'bukti'   => 'required|mimes:jpg,jpeg,png,webp,gif,bmp,tiff,heic,heif,pdf|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('bukti')) {
            $path = $request->file('bukti')->store('bukti_pengajuan', 'public');
        }

        PengajuanPresensi::create([
            'user_id' => Auth::id(),
            'jenis'   => $request->jenis,
            'tanggal' => $request->tanggal,
            'waktu'   => $request->waktu,
            'alasan'  => $request->alasan,
            'bukti'   => $path,
            'status'  => 'pending',
        ]);

        return redirect()->route('pegawai.pengajuan.index')
            ->with('success', 'Pengajuan berhasil dikirim.');
    }
}
