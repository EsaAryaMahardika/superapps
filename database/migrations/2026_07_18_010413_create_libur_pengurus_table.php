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
        Schema::create('libur_pengurus', function (Blueprint $table) {
            $table->id();
            $table->string('tanggal', 10);        // format dd-mm-yyyy
            $table->enum('tipe', ['bandongan', 'wirid', 'yasinan']);
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['tanggal', 'tipe']); // satu kegiatan per tanggal hanya bisa libur sekali
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libur_pengurus');
    }
};
