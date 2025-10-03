<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManajemenAdminController extends Controller
{
    /**
     * Menampilkan semua admin dan user yang bisa dijadikan admin
     */
    public function index()
    {
        $admins = User::where('role', 'admin')->orderBy('name')->get();
        $users = User::where('role', '!=', 'admin')->orderBy('name')->get(); // user non-admin
        return view('superadmin.manajemenadmin', compact('admins', 'users'));
    }

    /**
     * Menjadikan user yang sudah ada sebagai admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'can_approve_pengajuan' => 'sometimes|boolean',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->update([
            'role' => 'admin',
            'can_approve_pengajuan' => $request->has('can_approve_pengajuan') ? true : false,
        ]);

        return redirect()->route('superadmin.manajemenadmin.index')
            ->with('success', 'User berhasil dijadikan admin.');
    }

    /**
     * Toggle can_approve_pengajuan admin
     */
    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        // Toggle status
        $newStatus = $request->input('can_approve_pengajuan') == 1 ? true : false;
        $admin->update([
            'can_approve_pengajuan' => $newStatus,
        ]);

        return redirect()->route('superadmin.manajemenadmin.index')
            ->with('success', 'Status Approve admin berhasil diperbarui.');
    }

    /**
     * Hapus admin (kembalikan role menjadi pegawai)
     */
    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        $admin->update([
            'role' => 'pegawai',
            'can_approve_pengajuan' => false, // reset agar tidak bisa approve lagi
        ]);

        return redirect()->route('superadmin.manajemenadmin.index')
            ->with('success', 'Admin berhasil dihapus.');
    }

    /**
     * Reset password admin
     */
    public function resetPassword($id)
    {
        $admin = User::findOrFail($id);
        $admin->update([
            'password' => Hash::make('password123'), // password default
        ]);

        return redirect()->route('superadmin.manajemenadmin.index')
            ->with('success', 'Password berhasil di-reset menjadi "password123".');
    }
}
