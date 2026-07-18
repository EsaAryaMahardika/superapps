<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('username')->nullable();        // simpan username langsung agar log tetap ada jika user dihapus
            $table->string('role', 30)->nullable();
            $table->string('action', 100);                // e.g. 'login', 'absensi.store', 'pengurus.update'
            $table->string('description')->nullable();    // kalimat deskriptif
            $table->string('module', 50)->nullable();     // e.g. 'kepkam', 'mahadiyah', 'admin'
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('created_at');
            $table->index('module');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
