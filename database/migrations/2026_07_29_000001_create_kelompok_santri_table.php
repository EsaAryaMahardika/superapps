<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelompok_santri', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kepkam_nis'); // NIS pengurus (kepkam owner)
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::table('santri', function (Blueprint $table) {
            $table->unsignedBigInteger('kelompok_id')->nullable()->after('kepkam');
        });
    }

    public function down(): void
    {
        Schema::table('santri', function (Blueprint $table) {
            $table->dropColumn('kelompok_id');
        });
        Schema::dropIfExists('kelompok_santri');
    }
};
