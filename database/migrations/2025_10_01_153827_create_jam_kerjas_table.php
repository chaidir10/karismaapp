<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jam_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('hari'); // contoh: Senin, Selasa, Rabu...
            $table->time('jam_masuk'); 
            $table->time('jam_pulang'); 
            $table->timestamps();
        });

        // Insert default data
        DB::table('jam_kerja')->insert([
            [
                'hari' => 'Senin',
                'jam_masuk' => '07:30:00',
                'jam_pulang' => '16:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'hari' => 'Selasa',
                'jam_masuk' => '07:30:00',
                'jam_pulang' => '16:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'hari' => 'Rabu',
                'jam_masuk' => '07:30:00',
                'jam_pulang' => '16:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'hari' => 'Kamis',
                'jam_masuk' => '07:30:00',
                'jam_pulang' => '16:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'hari' => "Jum'at",
                'jam_masuk' => '07:30:00',
                'jam_pulang' => '16:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jam_kerja');
    }
};
