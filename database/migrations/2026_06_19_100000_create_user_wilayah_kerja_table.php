<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_wilayah_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('wilayah_kerja_id');
            $table->foreign('wilayah_kerja_id')->references('id')->on('wilayah_kerja')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'wilayah_kerja_id']);
        });

        // Migrasi data: unit_id lama → tabel pivot
        $users = DB::table('users')->whereNotNull('unit_id')->get();
        foreach ($users as $user) {
            DB::table('user_wilayah_kerja')->insert([
                'user_id' => $user->id,
                'wilayah_kerja_id' => $user->unit_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_wilayah_kerja');
    }
};
