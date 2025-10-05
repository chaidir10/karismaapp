<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JamKerja;
use App\Models\JamShift;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class JamKerjaController extends Controller
{
    /**
     * Tampilkan halaman manajemen jam kerja & jam shift.
     */
    public function index()
    {
        $jamKerja = JamKerja::all();
        $jamShift = JamShift::all();
        return view('admin.manajemenjamkerja', compact('jamKerja', 'jamShift'));
    }

    // =====================================================
    // =============== JAM KERJA NORMAL =====================
    // =====================================================

    public function show($id)
    {
        $jamKerja = JamKerja::findOrFail($id);
        return response()->json($jamKerja);
    }

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

    public function destroy($id)
    {
        $jamKerja = JamKerja::findOrFail($id);
        $jamKerja->delete();
        return response()->json(['success' => true]);
    }

    // =====================================================
    // ==================== JAM SHIFT ======================
    // =====================================================

    public function storeShift(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i|after:jam_masuk',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        JamShift::create($request->only('nama', 'jam_masuk', 'jam_pulang'));
        return response()->json(['success' => true]);
    }

    public function updateShift(Request $request, $id)
    {
        $jamShift = JamShift::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama' => [
                'required',
                'string',
                Rule::unique('jam_shift')->ignore($jamShift->id),
            ],
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i|after:jam_masuk',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jamShift->update($request->only('nama', 'jam_masuk', 'jam_pulang'));
        return response()->json(['success' => true]);
    }

    public function destroyShift($id)
    {
        $jamShift = JamShift::findOrFail($id);
        $jamShift->delete();
        return response()->json(['success' => true]);
    }
}
