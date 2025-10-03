<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayah_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('nama');        // Nama kantor/unit
            $table->text('alamat')->nullable();   // Alamat kantor
            $table->string('telepon')->nullable(); // Nomor telepon kantor
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah_kerja');
    }
};
