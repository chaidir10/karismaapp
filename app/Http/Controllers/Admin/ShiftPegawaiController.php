<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\JamShift;
use App\Models\WilayahKerja;
use Illuminate\Http\Request;

class ShiftPegawaiController extends Controller
{
    public function index()
    {
        $users = User::nonTester()
            ->with(['wilayahKerja', 'jamShift'])
            ->orderBy('name')
            ->get();

        $shifts = JamShift::orderBy('nama')->get();
        $units  = WilayahKerja::orderBy('nama')->get();

        return view('admin.shift-pegawai', compact('users', 'shifts', 'units'));
    }

    public function update(Request $request, $id)
    {
        $user = User::nonTester()->findOrFail($id);

        $request->validate([
            'can_shift'   => 'required|boolean',
            'jam_shift_id' => 'nullable|exists:jam_shift,id',
        ]);

        $canShift    = $request->boolean('can_shift');
        $jamShiftId  = $canShift ? $request->jam_shift_id : null;

        $user->update([
            'can_shift'    => $canShift,
            'jam_shift_id' => $jamShiftId,
        ]);

        $shift = $jamShiftId ? JamShift::find($jamShiftId) : null;

        return response()->json([
            'success'     => true,
            'message'     => 'Status shift berhasil diperbarui',
            'can_shift'   => $canShift,
            'shift_nama'  => $shift->nama ?? null,
            'jam_masuk'   => $shift->jam_masuk ?? null,
            'jam_pulang'  => $shift->jam_pulang ?? null,
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'user_ids'    => 'required|array|min:1',
            'user_ids.*'  => 'exists:users,id',
            'can_shift'   => 'required|boolean',
            'jam_shift_id' => 'nullable|exists:jam_shift,id',
        ]);

        $canShift   = $request->boolean('can_shift');
        $jamShiftId = $canShift ? $request->jam_shift_id : null;

        User::nonTester()
            ->whereIn('id', $request->user_ids)
            ->update([
                'can_shift'    => $canShift,
                'jam_shift_id' => $jamShiftId,
            ]);

        return response()->json([
            'success' => true,
            'message' => count($request->user_ids) . ' pegawai berhasil diperbarui',
        ]);
    }
}
