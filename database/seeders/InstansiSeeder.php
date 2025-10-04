<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Instansi;

class InstansiSeeder extends Seeder
{
    public function run(): void
    {
        Instansi::create([
            'nama' => 'Balai Kekarantinaan Kesehatan Kelas I Tarakan',
            'alamat' => 'Jalan Mulawarman No. 103 Kelurahan Karang Anyar Kecamatan Tarakan Barat Kota Tarakan Kalimantan Utara',
            'email' => 'kkp.tarakan.borneo@gmail.com',
            'no_hp' => '0811-5919-901',
            'fax' => '-',
            'kode_instansi' => 'TRK-KU01'
        ]);
    }
}
