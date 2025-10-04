<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Instansi;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Tampilkan halaman registrasi.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Tangani request registrasi baru.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input
        $request->validate([
            'nip' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'name' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'kode_instansi' => ['required', 'string', 'exists:instansi,kode_instansi'], // validasi kode_instansi
        ], [
            'kode_instansi.exists' => 'Kode instansi tidak valid. Silakan hubungi admin.',
        ]);

        // Ambil instansi berdasarkan kode
        $instansi = Instansi::where('kode_instansi', $request->kode_instansi)->first();

        // Buat user baru
        $user = User::create([
            'nip' => $request->nip,
            'name' => $request->name,
            'jabatan' => $request->jabatan,
            'email' => $request->email,
            'role' => 'pegawai', // otomatis pegawai
            'instansi_id' => $instansi->id, // relasi instansi
            'password' => Hash::make($request->password),
        ]);

        // Event registrasi (opsional, misal untuk listener email verifikasi)
        event(new Registered($user));

        // Redirect ke login dengan notifikasi sukses
        return redirect()->route('login')
            ->with('status', 'Registrasi berhasil! Silakan login ke akun Anda.');
    }
}
