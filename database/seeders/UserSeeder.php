<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Superadmin
        User::create([
            'nip' => '199800112025061001',
            'name' => 'Super Admin',
            'email' => 'superadmin@karisma.test',
            'password' => Hash::make('password'),
            'jabatan' => 'Super Admin',
            'unit_id' => 1,
            'jenis_pegawai' => 'ASN',
            'role' => 'superadmin',
            'can_approve_pengajuan' => true,
        ]);

        // Admin
        User::create([
            'nip' => '199800112025061002',
            'name' => 'Admin Satuan Kerja',
            'email' => 'admin@karisma.test',
            'password' => Hash::make('password'),
            'jabatan' => 'Admin Kepegawaian',
            'unit_id' => 1,
            'jenis_pegawai' => 'ASN',
            'role' => 'admin',
            'can_approve_pengajuan' => true,
        ]);

        // Pegawai
        User::create([
            'nip' => '199800112025061003',
            'name' => 'Pegawai Biasa',
            'email' => 'pegawai@karisma.test',
            'password' => Hash::make('password'),
            'jabatan' => 'Pranata Komputer Ahli Pertama',
            'unit_id' => 1,
            'jenis_pegawai' => 'ASN',
            'role' => 'pegawai',
            'can_approve_pengajuan' => false,
        ]);
    }
}
