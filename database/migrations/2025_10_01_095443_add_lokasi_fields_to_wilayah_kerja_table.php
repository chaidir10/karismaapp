<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom latitude, longitude, dan radius
     */
    public function up(): void
    {
        Schema::table('wilayah_kerja', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('nama');   // contoh: -3.3145
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude'); // contoh: 117.591
            $table->integer('radius')->default(100)->after('longitude'); // meter
        });
    }

    /**
     * Rollback perubahan
     */
    public function down(): void
    {
        Schema::table('wilayah_kerja', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'radius']);
        });
    }
};
