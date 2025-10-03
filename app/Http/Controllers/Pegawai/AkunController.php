<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AkunController extends Controller
{
    /**
     * Tampilkan halaman akun pegawai
     */
    public function index()
    {
        $user = Auth::user();
        return view('pegawai.akun', compact('user'));
    }

    /**
     * Update data akun pegawai
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // maksimal 2MB
            'alamat' => 'nullable|string|max:1000', // validasi alamat
        ]);

        // Update nama, email, dan alamat
        $user->name = $request->name;
        $user->email = $request->email;
        $user->alamat = $request->alamat;
         $user->no_hp = $request->no_hp;

        // Update password jika ada
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Update foto profil jika ada
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($user->foto_profil && Storage::disk('public')->exists('foto_profil/' . $user->foto_profil)) {
                Storage::disk('public')->delete('foto_profil/' . $user->foto_profil);
            }

            $file = $request->file('foto_profil');
            $filename = time() . '_' . $file->getClientOriginalName();
            Storage::disk('public')->putFileAs('foto_profil', $file, $filename);

            $user->foto_profil = $filename;
        }

        $user->save();

        return redirect()->route('pegawai.akun.index')->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Logout pegawai
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
