<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('issue_type', 30);
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('reported_at');
            $table->timestamp('resolved_at')->nullable();
            $table->index(['user_id', 'issue_type']);
            $table->index('reported_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_issues');
    }
};
