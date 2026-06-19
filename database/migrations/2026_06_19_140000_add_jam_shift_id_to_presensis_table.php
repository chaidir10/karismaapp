<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->unsignedBigInteger('jam_shift_id')->nullable()->after('is_lembur');
            $table->foreign('jam_shift_id')->references('id')->on('jam_shift')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->dropForeign(['jam_shift_id']);
            $table->dropColumn('jam_shift_id');
        });
    }
};
