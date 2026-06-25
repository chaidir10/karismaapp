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

        $allPegawai = collect();
        if (in_array($user->role, ['admin', 'superadmin'])) {
            $allPegawai = \App\Models\User::nonTester()->orderBy('name')->get(['id', 'name', 'nip']);
        }

        return view('pegawai.akun', compact('user', 'allPegawai'));
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
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'alamat' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Nama wajib diisi!',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',

            'email.required' => 'Email wajib diisi!',
            'email.email' => 'Format email tidak valid!',
            'email.unique' => 'Email sudah terdaftar!',

            'no_hp.max' => 'Nomor HP maksimal 20 karakter.',

            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',

            'foto_profil.image' => 'File harus berupa gambar.',
            'foto_profil.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'foto_profil.max' => 'Ukuran gambar maksimal 2MB.',

            'alamat.max' => 'Alamat maksimal 1000 karakter.',
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

    public function toggleDarurat(Request $request)
    {
        return $this->saveSetting($request);
    }

    public function saveSetting(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $allowed = [
            'disable_presensi_hari_libur',
            'enable_face_detection',
            'face_detection_mode',
            'face_detection_users',
            'require_masuk_before_pulang',
            'enable_work_timer',
            'enable_absen_darurat',
            'absen_darurat_mode',
            'absen_darurat_users',
            'face_detection_users_except',
            'face_detection_users_only',
            'absen_darurat_users_except',
            'absen_darurat_users_only',
        ];

        $key = $request->input('key');
        if (!in_array($key, $allowed)) {
            return response()->json(['error' => 'Invalid key'], 422);
        }

        \App\Models\AppSetting::setValue($key, $request->input('value'));
        return response()->json(['ok' => true]);
    }

    public function resetPresensi(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_tester) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $today = now()->toDateString();
        $type = $request->input('type', 'all');
        $query = \App\Models\Presensi::where('user_id', $user->id)->whereDate('tanggal', $today);

        if ($type === 'reguler') {
            $count = $query->where('is_lembur', false)->delete();
        } elseif ($type === 'lembur') {
            $count = $query->where('is_lembur', true)->delete();
        } else {
            $count = $query->delete();
        }

        return response()->json(['ok' => true, 'message' => $count . ' data presensi dihapus']);
    }

    public function simulasiTerpenuhi(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_tester) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $today = now()->format('Y-m-d');
        $jadwal = $user->getJadwalKerja($today);
        $jamMasukJadwal = $jadwal['jam_masuk'];
        $jamPulangJadwal = $jadwal['jam_pulang'];
        $mode = $request->input('mode', 'terpenuhi');

        if ($mode === 'jadwal') {
            $masukSimulasi = $jamMasukJadwal;
            $label = 'jam jadwal (' . \Carbon\Carbon::parse($masukSimulasi)->format('H:i') . ')';
        } elseif ($mode === 'custom') {
            $jam = $request->input('jam');
            if (!$jam) {
                return response()->json(['ok' => false, 'message' => 'Jam tidak valid'], 422);
            }
            $masukSimulasi = $jam . ':00';
            $label = \Carbon\Carbon::parse($masukSimulasi)->format('H:i');
        } else {
            $durasiDetik = abs(strtotime($jamPulangJadwal) - strtotime($jamMasukJadwal));
            $jadwalStart = \Carbon\Carbon::parse($jamMasukJadwal);
            $elapsedFromJadwal = abs(now()->diffInSeconds($jadwalStart));
            if ($elapsedFromJadwal >= $durasiDetik) {
                $masukSimulasi = $jamMasukJadwal;
                $label = \Carbon\Carbon::parse($masukSimulasi)->format('H:i') . ' — jam terpenuhi, siap pulang';
            } else {
                $sisa = $durasiDetik - $elapsedFromJadwal;
                $sisaJam = floor($sisa / 3600);
                $sisaMenit = floor(($sisa % 3600) / 60);
                $masukSimulasi = $jamMasukJadwal;
                $label = 'Masuk di-set ke ' . \Carbon\Carbon::parse($masukSimulasi)->format('H:i') . ' — terpenuhi dalam ' . ($sisaJam > 0 ? $sisaJam . 'j ' : '') . $sisaMenit . 'm lagi';
            }
        }

        \App\Models\Presensi::updateOrCreate(
            ['user_id' => $user->id, 'tanggal' => $today, 'jenis' => 'masuk', 'is_lembur' => false],
            ['jam' => $masukSimulasi, 'status' => 'approved', 'lokasi' => null, 'foto' => null]
        );

        \App\Models\Presensi::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->where('jenis', 'pulang')
            ->where('is_lembur', false)
            ->delete();

        return response()->json(['ok' => true, 'message' => 'Masuk di-set ke ' . $label]);
    }

    public function updateJamPresensi(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_tester) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'id' => 'required|integer',
            'jam' => 'required|date_format:H:i',
        ]);

        $presensi = \App\Models\Presensi::where('user_id', $user->id)->findOrFail($request->id);
        $presensi->jam = $request->jam . ':00';
        $presensi->save();

        return response()->json(['ok' => true, 'message' => ucfirst($presensi->jenis) . ' diubah ke ' . $request->jam]);
    }

    public function setWilayah(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_tester) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $ids = $request->input('wilayah_ids', []);
        $user->wilayahKerjaList()->sync($ids);

        return response()->json(['ok' => true, 'message' => count($ids) . ' wilayah dipilih']);
    }
}
