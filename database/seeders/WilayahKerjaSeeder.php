<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('wilayah_kerja')->insert([
            [
                'nama' => 'Kantor Induk',
                'alamat' => 'Jl. Mulawarman 103, Tarakan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Wilker Berau',
                'alamat' => 'Jl. Mawar II, Gayam, Kec. Tj. Redeb, Kabupaten Berau, Kalimantan Timur 77315',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Wilker Bunyu',
                'alamat' => 'Jl. Pangkalan, Bunyu Baru, Bunyu, Kabupaten Bulungan, Kalimantan Utara 77281',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Wilker Malinau',
                'alamat' => 'Malinau, North Kalimantan, Kalimantan, 77554, Indonesia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Wilker Nunukan',
                'alamat' => 'East Nunukan, Nunukan, Nunukan Regency, North Kalimantan 77482',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Wilker Pelabuhan Laut Tarakan',
                'alamat' => 'Jalan Yos Sudarso, Karang Balik, Tarakan, North Kalimantan, Kalimantan, 77126, Indonesia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Wilker Sebatik',
                'alamat' => 'Sei, Jl. Ahmad Yani Desa No.22 RT 04, Pancang, Lumbis Ogong, Kabupaten Nunukan, Kalimantan Utara 77483',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Wilker Tanjung Selor',
                'alamat' => 'Jl. Sabanar Lama, RT.67/RW.25, Tj. Selor Hilir, Kec. Tj. Selor, Kabupaten Bulungan, Kalimantan Utara 77211',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
