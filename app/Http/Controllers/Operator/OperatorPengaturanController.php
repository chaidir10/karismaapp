<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Instansi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OperatorPengaturanController extends Controller
{
    public function index()
    {
        $settings = AppSetting::all()->pluck('value', 'key');
        $instansi = Instansi::first();
        $pegawai = User::nonTester()->orderBy('name')->get(['id', 'name', 'nip']);

        return view('operator.pengaturan', compact('settings', 'instansi', 'pegawai'));
    }

    public function updateInstansi(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode_instansi' => 'required|string|max:50',
            'alamat' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $instansi = Instansi::first();
        if ($instansi) {
            $instansi->update($request->only('nama', 'kode_instansi', 'alamat', 'email', 'no_hp'));
        } else {
            Instansi::create($request->only('nama', 'kode_instansi', 'alamat', 'email', 'no_hp'));
        }

        return redirect()->route('operator.pengaturan.index')->with('success', 'Data instansi berhasil diperbarui');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate(['app_logo' => 'required|image|mimes:png,jpg,jpeg,webp,svg|max:2048']);

        $file = $request->file('app_logo');
        $path = $file->store('logo', 'public');

        $old = AppSetting::getValue('app_logo');
        if ($old && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
        }

        AppSetting::setValue('app_logo', $path);

        return redirect()->route('operator.pengaturan.index')->with('success', 'Logo berhasil diperbarui');
    }

    public function removeLogo()
    {
        $old = AppSetting::getValue('app_logo');
        if ($old && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
        }
        AppSetting::setValue('app_logo', '');

        return redirect()->route('operator.pengaturan.index')->with('success', 'Logo berhasil dihapus');
    }

    public function updateSettings(Request $request)
    {
        AppSetting::setValue('disable_presensi_hari_libur', $request->boolean('disable_presensi_hari_libur') ? '1' : '0');
        AppSetting::setValue('enable_face_detection', $request->boolean('enable_face_detection') ? '1' : '0');
        AppSetting::setValue('face_detection_mode', $request->input('face_detection_mode', 'all'));
        AppSetting::setValue('face_detection_users_except', json_encode($request->input('face_detection_users_except', [])));
        AppSetting::setValue('face_detection_users_only', json_encode($request->input('face_detection_users_only', [])));
        AppSetting::setValue('require_masuk_before_pulang', $request->boolean('require_masuk_before_pulang') ? '1' : '0');
        AppSetting::setValue('show_notif_banner', $request->boolean('show_notif_banner') ? '1' : '0');
        AppSetting::setValue('enable_work_timer', $request->boolean('enable_work_timer') ? '1' : '0');
        AppSetting::setValue('enable_absen_darurat', $request->boolean('enable_absen_darurat') ? '1' : '0');
        AppSetting::setValue('absen_darurat_mode', $request->input('absen_darurat_mode', 'all'));
        AppSetting::setValue('absen_darurat_users_except', json_encode($request->input('absen_darurat_users_except', [])));
        AppSetting::setValue('absen_darurat_users_only', json_encode($request->input('absen_darurat_users_only', [])));

        return redirect()->route('operator.pengaturan.index')->with('success', 'Pengaturan berhasil disimpan');
    }
}
