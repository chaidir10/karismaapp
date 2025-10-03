<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pengajuan_presensi', function (Blueprint $table) {
            $table->string('bukti')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('pengajuan_presensi', function (Blueprint $table) {
            $table->string('bukti')->nullable(false)->change();
        });
    }
};
