<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WilayahKerja;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\JamShift;
use App\Models\UserJamShift;


class ManajemenPegawaiController extends Controller
{
    /**
     * Tampilkan halaman manajemen pegawai (Blade)
     */
    public function index()
    {
        $users = User::with('wilayahKerja')->get();
        $units = WilayahKerja::all();

        return view('admin.manajemenpegawai', compact('users', 'units'));
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
            'jabatan' => 'nullable|string|max:255',
            'jenis_pegawai' => 'required|in:asn,non_asn,outsourcing',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'errors' => $validator->errors()
            ], 422);
        }

        User::create([
            'nip' => $request->nip,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'unit_id' => $request->unit_id,
            'jabatan' => $request->jabatan,
            'jenis_pegawai' => $request->jenis_pegawai,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

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
        $user = User::with('wilayahKerja')->findOrFail($id);

        return response()->json($user);
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
            'jabatan' => 'nullable|string|max:255',
            'jenis_pegawai' => 'required|in:asn,non_asn,outsourcing',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
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

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

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
