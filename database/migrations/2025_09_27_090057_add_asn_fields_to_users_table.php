<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 20)->unique()->after('id');
            $table->string('jabatan', 100)->nullable()->after('name');
            $table->unsignedBigInteger('unit_id')->nullable()->after('jabatan');
            $table->enum('jenis_pegawai', ['asn', 'non_asn', 'outsourcing'])
                ->default('asn')
                ->after('unit_id');
            $table->enum('role', ['pegawai', 'admin', 'superadmin'])->default('pegawai')->after('unit_id');
            $table->boolean('can_approve_pengajuan')->default(false)->after('role');
            $table->string('foto_profil')->nullable()->after('can_approve_pengajuan');
            $table->string('no_hp', 20)->nullable()->after('foto_profil');

            // relasi ke wilayah_kerja
            $table->foreign('unit_id')->references('id')->on('wilayah_kerja')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn([
                'nip',
                'jabatan',
                'unit_id',
                'jenis_pegawai',
                'role',
                'can_approve_pengajuan',
                'foto_profil',
                'no_hp'
            ]);
        });
    }
};
