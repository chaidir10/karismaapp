<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengajuanPresensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
    public function index()
    {
        $pengajuan = PengajuanPresensi::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pegawai.pengajuan', compact('pengajuan'));
    }

    public function create()
    {
        return view('pegawai.pengajuan');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis'   => 'required|in:masuk,pulang,keduanya',
            'tanggal' => 'required|date',
            'alasan'  => 'required|string|max:255',
            'bukti'   => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // default null
        $path = null;

        // hanya simpan file kalau ada
        if ($request->hasFile('bukti')) {
            $path = $request->file('bukti')->store('bukti_pengajuan', 'public');
        }

        PengajuanPresensi::create([
            'user_id' => Auth::id(),
            'jenis'   => $request->jenis,
            'tanggal' => $request->tanggal,
            'alasan'  => $request->alasan,
            'bukti'   => $path, // bisa null kalau tidak upload
            'status'  => 'pending',
        ]);

        return redirect()->route('pegawai.pengajuan.index')
            ->with('success', 'Pengajuan berhasil dikirim.');
    }
}
