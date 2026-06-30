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
        $users  = User::nonTester()->where('can_shift', true)
            ->with(['wilayahKerja', 'jamShift'])
            ->orderBy('name')
            ->get();

        $shifts   = JamShift::orderBy('nama')->get();
        $units    = WilayahKerja::orderBy('nama')->get();
        $shiftMap = $shifts->mapWithKeys(fn($s) => [
            $s->id => [
                'id'        => $s->id,
                'nama'      => $s->nama,
                'jam_masuk' => $s->jam_masuk,
                'jam_pulang'=> $s->jam_pulang,
            ]
        ]);

        return view('admin.shift-pegawai', compact('users', 'shifts', 'units', 'shiftMap'));
    }

    public function nonShiftUsers()
    {
        $users = User::nonTester()->where('can_shift', false)
            ->with('wilayahKerja')
            ->orderBy('name')
            ->get()
            ->map(fn($u) => [
                'id'     => $u->id,
                'name'   => $u->name,
                'nip'    => $u->nip,
                'unit'   => $u->wilayahKerja->nama ?? '-',
                'foto'   => $u->foto_profil ? asset('public/storage/foto_profil/' . $u->foto_profil) : null,
                'inisial'=> strtoupper(substr($u->name, 0, 1)),
            ]);

        return response()->json($users);
    }

    public function assign(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'jam_shift_id' => 'required|exists:jam_shift,id',
        ]);

        $user = User::nonTester()->findOrFail($request->user_id);
        $user->update(['can_shift' => true, 'jam_shift_id' => $request->jam_shift_id]);

        $shift = JamShift::find($request->jam_shift_id);

        return response()->json([
            'success'    => true,
            'user'       => [
                'id'       => $user->id,
                'name'     => $user->name,
                'nip'      => $user->nip,
                'unit'     => $user->wilayahKerja->nama ?? '-',
                'foto'     => $user->foto_profil ? asset('public/storage/foto_profil/' . $user->foto_profil) : null,
                'inisial'  => strtoupper(substr($user->name, 0, 1)),
                'shift_nama'   => $shift->nama,
                'jam_masuk'    => $shift->jam_masuk,
                'jam_pulang'   => $shift->jam_pulang,
                'jam_shift_id' => $shift->id,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jam_shift_id' => 'required|exists:jam_shift,id',
        ]);

        $user = User::nonTester()->findOrFail($id);
        $user->update(['can_shift' => true, 'jam_shift_id' => $request->jam_shift_id]);

        $shift = JamShift::find($request->jam_shift_id);

        return response()->json([
            'success'    => true,
            'shift_nama' => $shift->nama,
            'jam_masuk'  => $shift->jam_masuk,
            'jam_pulang' => $shift->jam_pulang,
        ]);
    }

    public function remove($id)
    {
        $user = User::nonTester()->findOrFail($id);
        $user->update(['can_shift' => false, 'jam_shift_id' => null]);

        return response()->json(['success' => true]);
    }
}
