<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WilayahKerja;

class ManajemenLokasiController extends Controller
{
    /**
     * Tampilkan halaman manajemen lokasi
     */
    public function index()
    {
        $lokasi = WilayahKerja::all();
        return view('admin.manajemenlokasi', compact('lokasi'));
    }

    /**
     * Simpan data lokasi baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'alamat'    => 'nullable|string',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius'    => 'required|integer|min:0',
        ]);

        $lokasi = WilayahKerja::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lokasi berhasil ditambahkan',
                'data'    => $lokasi
            ]);
        }

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi berhasil ditambahkan');
    }

    /**
     * Update lokasi
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'alamat'    => 'nullable|string',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius'    => 'required|integer|min:0',
        ]);

        $lokasi = WilayahKerja::findOrFail($id);
        $lokasi->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lokasi berhasil diperbarui',
                'data'    => $lokasi
            ]);
        }

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi berhasil diperbarui');
    }

    /**
     * Hapus lokasi
     */
    public function destroy($id)
    {
        $lokasi = WilayahKerja::findOrFail($id);
        $lokasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lokasi berhasil dihapus'
        ]);
    }

    /**
     * Ambil detail lokasi (AJAX)
     */
    public function show($id)
    {
        $lokasi = WilayahKerja::findOrFail($id);
        return response()->json($lokasi);
    }
}
