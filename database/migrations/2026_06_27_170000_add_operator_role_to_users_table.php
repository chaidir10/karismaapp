<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pegawai','admin','superadmin','operator') NOT NULL DEFAULT 'pegawai'");
    }

    public function down(): void
    {
        // Pastikan tidak ada data operator sebelum rollback enum
        DB::statement("UPDATE users SET role = 'pegawai' WHERE role = 'operator'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pegawai','admin','superadmin') NOT NULL DEFAULT 'pegawai'");
    }
};
