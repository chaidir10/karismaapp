<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WilayahKerja;
use App\Models\JamShift;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ManajemenPegawaiController extends Controller
{
    /**
     * Tampilkan halaman manajemen pegawai (Blade)
     */
    public function index()
    {
        $users = User::with(['wilayahKerja', 'wilayahKerjaList', 'jamShift'])->get();
        $units = WilayahKerja::all();
        $shifts = JamShift::all();

        return view('admin.manajemenpegawai', compact('users', 'units', 'shifts'));
    }

    /**
     * Simpan pegawai baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'required|numeric|unique:users,nip',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'unit_id' => 'nullable|exists:wilayah_kerja,id',
            'wilayah_ids' => 'nullable|array',
            'wilayah_ids.*' => 'exists:wilayah_kerja,id',
            'jabatan' => 'nullable|string|max:255',
            'jenis_pegawai' => 'required|in:asn,non_asn,outsourcing',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'can_shift' => 'nullable|boolean',
            'jam_shift_id' => 'nullable|exists:jam_shift,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'nip' => $request->nip,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'unit_id' => $request->unit_id,
            'jabatan' => $request->jabatan,
            'jenis_pegawai' => $request->jenis_pegawai,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'can_shift' => $request->boolean('can_shift', false),
        ]);

        if ($request->has('wilayah_ids')) {
            $user->wilayahKerjaList()->sync($request->wilayah_ids);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pegawai berhasil ditambahkan'
        ]);
    }

    /**
     * Tampilkan detail pegawai (untuk modal)
     */
    public function show($id)
    {
        $user = User::with(['wilayahKerja', 'wilayahKerjaList', 'jamShift'])->findOrFail($id);

        $data = $user->toArray();
        $data['wilayah_ids'] = $user->wilayahKerjaList->pluck('id')->toArray();
        $data['shift_nama'] = $user->jamShift->nama ?? null;

        return response()->json($data);
    }

    /**
     * Update pegawai
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nip' => 'required|numeric|unique:users,nip,'.$user->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6',
            'unit_id' => 'nullable|exists:wilayah_kerja,id',
            'wilayah_ids' => 'nullable|array',
            'wilayah_ids.*' => 'exists:wilayah_kerja,id',
            'jabatan' => 'nullable|string|max:255',
            'jenis_pegawai' => 'required|in:asn,non_asn,outsourcing',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'can_shift' => 'nullable|boolean',
            'jam_shift_id' => 'nullable|exists:jam_shift,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->nip = $request->nip;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->unit_id = $request->unit_id;
        $user->jabatan = $request->jabatan;
        $user->jenis_pegawai = $request->jenis_pegawai;
        $user->no_hp = $request->no_hp;
        $user->alamat = $request->alamat;
        $user->can_shift = $request->boolean('can_shift', false);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        $user->wilayahKerjaList()->sync($request->wilayah_ids ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Pegawai berhasil diperbarui'
        ]);
    }

    /**
     * Hapus pegawai
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Pegawai berhasil dihapus'
        ]);
    }

    /**
     * Reset password ke NIP
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->password = Hash::make($user->nip);
        $user->save();

        return response()->json([
            'success' => true, 
            'message' => 'Password berhasil direset menjadi NIP'
        ]);
    }
}
