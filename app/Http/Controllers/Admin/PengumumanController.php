<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumumans = Pengumuman::orderBy('created_at', 'desc')->get();
        $jenisOptions = Pengumuman::jenisOptions();
        return view('admin.pengumuman', compact('pengumumans', 'jenisOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'jenis' => 'required|string',
            'isi' => 'required|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'waktu' => 'nullable|date_format:H:i',
            'gambar' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg,bmp|max:5120',
        ]);

        $data = $request->only(['judul', 'jenis', 'isi', 'tanggal_mulai', 'tanggal_selesai', 'waktu']);
        $data['is_active'] = true;

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('pengumuman', 'public');
        }

        Pengumuman::create($data);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan');
    }

    public function show($id)
    {
        return response()->json(Pengumuman::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'jenis' => 'required|string',
            'isi' => 'required|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'waktu' => 'nullable|date_format:H:i',
            'gambar' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg,bmp|max:5120',
        ]);

        $pengumuman = Pengumuman::findOrFail($id);
        $data = $request->only(['judul', 'jenis', 'isi', 'tanggal_mulai', 'tanggal_selesai', 'waktu']);

        if ($request->hasFile('gambar')) {
            if ($pengumuman->gambar && Storage::disk('public')->exists($pengumuman->gambar)) {
                Storage::disk('public')->delete($pengumuman->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('pengumuman', 'public');
        }

        if ($request->has('hapus_gambar') && !$request->hasFile('gambar')) {
            if ($pengumuman->gambar && Storage::disk('public')->exists($pengumuman->gambar)) {
                Storage::disk('public')->delete($pengumuman->gambar);
            }
            $data['gambar'] = null;
        }

        $pengumuman->update($data);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui');
    }

    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        if ($pengumuman->gambar && Storage::disk('public')->exists($pengumuman->gambar)) {
            Storage::disk('public')->delete($pengumuman->gambar);
        }
        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus');
    }

    public function toggle($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->update(['is_active' => !$pengumuman->is_active]);
        return response()->json(['success' => true, 'is_active' => $pengumuman->is_active]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate(['image' => 'required|file|mimes:jpg,jpeg,png,gif,webp,svg,bmp|max:5120']);
        $path = $request->file('image')->store('pengumuman/content', 'public');
        return response()->json(['url' => asset('public/storage/' . $path)]);
    }
}
