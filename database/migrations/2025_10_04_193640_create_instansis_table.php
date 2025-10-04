<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instansi', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('alamat')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('fax', 20)->nullable();
            $table->string('kode_instansi')->unique(); // kode unik untuk registrasi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instansi');
    }
};
