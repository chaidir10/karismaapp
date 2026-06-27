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
        $admins = User::whereIn('role', ['admin', 'operator'])->orderBy('name')->get();
        $users = User::whereNotIn('role', ['admin', 'operator'])->orderBy('name')->get(); // user non-admin/operator
        return view('superadmin.manajemenadmin', compact('admins', 'users'));
    }

    /**
     * Menjadikan user yang sudah ada sebagai admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_target' => 'required|in:admin,operator',
            'can_approve_pengajuan' => 'sometimes|boolean',
        ]);

        $user = User::findOrFail($request->user_id);
        $roleTarget = $request->input('role_target', 'admin');

        $user->update([
            'role' => $roleTarget,
            'can_approve_pengajuan' => $roleTarget === 'admin'
                ? ($request->has('can_approve_pengajuan') ? true : false)
                : false,
        ]);

        return redirect()->route('superadmin.manajemenadmin.index')
            ->with('success', 'User berhasil dijadikan ' . $roleTarget . '.');
    }

    /**
     * Toggle can_approve_pengajuan admin
     */
    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $request->validate([
            'role' => 'nullable|in:admin,operator',
            'can_approve_pengajuan' => 'nullable|boolean',
        ]);

        if ($request->filled('role')) {
            $newRole = $request->input('role');
            $admin->role = $newRole;
            if ($newRole === 'operator') {
                $admin->can_approve_pengajuan = false;
            }
        }

        if ($request->has('can_approve_pengajuan') && $admin->role === 'admin') {
            $admin->can_approve_pengajuan = $request->input('can_approve_pengajuan') == 1 ? true : false;
        }

        $admin->save();

        return redirect()->route('superadmin.manajemenadmin.index')
            ->with('success', 'Data role/approve berhasil diperbarui.');
    }

    /**
     * Hapus admin/operator (kembalikan role menjadi pegawai)
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
