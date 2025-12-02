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
            'jenis'       => 'required|in:masuk,pulang,keduanya',
            'tanggal'     => 'required|date',
            'alasan'      => 'required|string',
            'bukti'       => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',

            // tambahkan validasi jam (nullable saja)
            'jam_masuk'   => 'nullable|date_format:H:i',
            'jam_keluar'  => 'nullable|date_format:H:i',
        ]);

        $path = null;

        if ($request->hasFile('bukti')) {
            $path = $request->file('bukti')->store('bukti_pengajuan', 'public');
        }

        PengajuanPresensi::create([
            'user_id'     => Auth::id(),
            'jenis'       => $request->jenis,
            'tanggal'     => $request->tanggal,
            'alasan'      => $request->alasan,
            'bukti'       => $path,
            'status'      => 'pending',

            // tambahkan jam masuk / pulang
            'jam_masuk'   => $request->jam_masuk,
            'jam_keluar'  => $request->jam_keluar,

            'approved_by' => null,
            'approved_at' => null,
        ]);

        return redirect()->route('pegawai.pengajuan.index')
            ->with('success', 'Pengajuan berhasil dikirim.');
    }
}
