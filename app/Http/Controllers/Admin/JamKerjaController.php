<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JamKerja;
use App\Models\JamShift;
use App\Models\CustomHoliday;
use App\Helpers\HolidayHelper;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class JamKerjaController extends Controller
{
    /**
     * Halaman utama: menampilkan jam kerja normal & jam shift.
     */
    public function index()
    {
        $jamKerja = JamKerja::all();
        $jamShift = JamShift::all();
        $year = (int) request('year', date('Y'));
        $holidays = CustomHoliday::whereYear('date', $year)->orderBy('date')->paginate(7, ['*'], 'holiday_page');
        $holidays->appends(['year' => $year]);
        if ($holidays->isEmpty() && !request()->has('holiday_page')) {
            HolidayHelper::syncFromApi($year);
            $holidays = CustomHoliday::whereYear('date', $year)->orderBy('date')->paginate(7, ['*'], 'holiday_page');
            $holidays->appends(['year' => $year]);
        }
        return view('admin.manajemenjamkerja', compact('jamKerja', 'jamShift', 'holidays', 'year'));
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

        JamKerja::create([
            'hari' => $request->hari,
            'jam_masuk' => $request->jam_masuk,
            'jam_pulang' => $request->jam_pulang,
        ]);

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

        $jamKerja->update([
            'hari' => $request->hari,
            'jam_masuk' => $request->jam_masuk,
            'jam_pulang' => $request->jam_pulang,
        ]);

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

    public function showShift($id)
    {
        $shift = JamShift::find($id);

        if (!$shift) {
            return response()->json(['error' => 'Shift tidak ditemukan'], 404);
        }

        return response()->json($shift);
    }

    public function storeShift(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100|unique:jam_shift,nama',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i|after:jam_masuk',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        JamShift::create([
            'nama' => $request->nama,
            'jam_masuk' => $request->jam_masuk,
            'jam_pulang' => $request->jam_pulang,
        ]);

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

        $jamShift->update([
            'nama' => $request->nama,
            'jam_masuk' => $request->jam_masuk,
            'jam_pulang' => $request->jam_pulang,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroyShift($id)
    {
        $jamShift = JamShift::findOrFail($id);
        $jamShift->delete();
        return response()->json(['success' => true]);
    }

    // =====================================================
    // =============== HARI LIBUR ==========================
    // =====================================================

    public function syncHolidays(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        \Illuminate\Support\Facades\Cache::forget("holidays_api_{$year}");
        $count = HolidayHelper::syncFromApi($year);
        return redirect(route('admin.jamkerja.index', ['year' => $year]) . '#libur')
            ->with('success', $count > 0 ? "{$count} libur baru disinkronkan dari API" : "Data libur {$year} sudah terbaru");
    }

    public function storeHoliday(Request $request)
    {
        $request->validate([
            'date' => 'required|date|unique:custom_holidays,date',
            'name' => 'required|string|max:255',
        ]);
        CustomHoliday::create([
            'date' => $request->date,
            'name' => $request->name,
            'source' => 'manual',
            'is_active' => true,
        ]);
        $year = \Carbon\Carbon::parse($request->date)->year;
        return redirect(route('admin.jamkerja.index', ['year' => $year]) . '#libur')->with('success', 'Hari libur berhasil ditambahkan');
    }

    public function updateHoliday(Request $request, $id)
    {
        $holiday = CustomHoliday::findOrFail($id);
        $request->validate([
            'date' => 'required|date|unique:custom_holidays,date,' . $id,
            'name' => 'required|string|max:255',
        ]);
        $holiday->update($request->only('date', 'name'));
        $year = \Carbon\Carbon::parse($request->date)->year;
        return redirect(route('admin.jamkerja.index', ['year' => $year]) . '#libur')->with('success', 'Hari libur berhasil diperbarui');
    }

    public function destroyHoliday($id)
    {
        $holiday = CustomHoliday::findOrFail($id);
        $holiday->delete();
        return response()->json(['success' => true]);
    }

    public function toggleHoliday($id)
    {
        $holiday = CustomHoliday::findOrFail($id);
        $holiday->update(['is_active' => !$holiday->is_active]);
        return response()->json(['success' => true, 'is_active' => $holiday->is_active]);
    }
}
