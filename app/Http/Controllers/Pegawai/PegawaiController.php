<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WilayahKerja;

class PegawaiController extends Controller
{
    public function index()
    {
        // Ambil semua pegawai beserta wilayah kerja mereka
        $pegawai = User::with('wilayahKerja')->get();

        return view('pegawai.pegawai', compact('pegawai'));
    }
}
