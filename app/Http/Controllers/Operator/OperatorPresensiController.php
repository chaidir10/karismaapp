<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OperatorPresensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Presensi::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('tanggal', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('tanggal', '<=', $request->date_to);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('nip', 'like', "%{$s}%");
            });
        }

        $presensi = $query->latest('tanggal')->latest('jam')->paginate(25)->withQueryString();
        $pegawai = User::where('role', 'pegawai')->orderBy('name')->get(['id', 'name', 'nip']);

        return view('operator.manajemen-presensi', compact('presensi', 'pegawai'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required',
            'jenis' => 'required|in:masuk,pulang',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $presensi = Presensi::findOrFail($id);
        $presensi->update([
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'jenis' => $request->jenis,
            'status' => $request->status,
        ]);

        return redirect()->route('operator.presensi.index')->with('success', 'Data presensi berhasil diperbarui');
    }

    public function destroy($id)
    {
        $presensi = Presensi::findOrFail($id);
        $presensi->delete();

        return redirect()->route('operator.presensi.index')->with('success', 'Data presensi berhasil dihapus');
    }
}
