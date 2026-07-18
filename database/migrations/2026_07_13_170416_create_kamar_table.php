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
        if (!Schema::hasTable('kamar')) {
            Schema::create('kamar', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('asrama_id');
                $table->string('nama');
                $table->string('kepkam_nis', 20)->nullable();
                $table->timestamps();
            });
        }
        // FK tidak dibuat via migration karena type/charset mismatch dengan tabel lama
        // Relasi tetap berjalan lewat Eloquent
    }

    public function down(): void
    {
        Schema::dropIfExists('kamar');
    }
};
