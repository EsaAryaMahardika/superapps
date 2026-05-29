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
        Schema::table('pengurus', function (Blueprint $table) {
            // Hapus kolom jabatan & tempat lama (string) yang ditambah sebelumnya
            if (Schema::hasColumn('pengurus', 'jabatan')) {
                $table->dropColumn('jabatan');
            }
            if (Schema::hasColumn('pengurus', 'tempat')) {
                $table->dropColumn('tempat');
            }
            // Tambah foreign key ke tabel jabatan
            $table->foreignId('jabatan_id')->nullable()->after('nama')->constrained('jabatan')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengurus', function (Blueprint $table) {
            $table->dropForeign(['jabatan_id']);
            $table->dropColumn('jabatan_id');
            $table->string('jabatan')->nullable()->after('nama');
            $table->string('tempat')->nullable()->after('jabatan');
        });
    }
};
