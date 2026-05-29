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
        // Buat ulang tabel jabatan dengan struktur yang benar
        Schema::dropIfExists('jabatan');
        Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('divisi_id')->constrained('divisi')->cascadeOnDelete();
            $table->string('nama');   // Ketua, Sekretaris, Bendahara, Kepala Kamar 1 SMP, dll
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatan');
    }
};
