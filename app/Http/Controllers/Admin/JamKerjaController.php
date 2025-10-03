<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JamKerja;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class JamKerjaController extends Controller
{
    /**
     * Tampilkan halaman manajemen jam kerja.
     */
    public function index()
    {
        $jamKerja = JamKerja::all();
        return view('admin.manajemenjamkerja', compact('jamKerja'));
    }

    /**
     * Ambil data jam kerja berdasarkan ID (untuk modal edit).
     */
    public function show($id)
    {
        $jamKerja = JamKerja::findOrFail($id);
        return response()->json($jamKerja);
    }

    /**
     * Simpan jam kerja baru via AJAX.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hari' => 'required|string|unique:jam_kerja,hari',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i|after:jam_masuk',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        JamKerja::create($request->only('hari', 'jam_masuk', 'jam_pulang'));

        return response()->json(['success' => true]);
    }

    /**
     * Update jam kerja via AJAX berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        $jamKerja = JamKerja::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'hari' => [
                'required',
                'string',
                Rule::unique('jam_kerja')->ignore($jamKerja->id),
            ],
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i|after:jam_masuk',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jamKerja->update($request->only('hari', 'jam_masuk', 'jam_pulang'));

        return response()->json(['success' => true]);
    }

    /**
     * Hapus jam kerja via AJAX berdasarkan ID.
     */
    public function destroy($id)
    {
        $jamKerja = JamKerja::findOrFail($id);
        $jamKerja->delete();

        return response()->json(['success' => true]);
    }
}
