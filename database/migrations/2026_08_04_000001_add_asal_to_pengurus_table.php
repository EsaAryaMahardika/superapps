<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Asal pengurus: 'dalam' = pengurus dalam pondok, 'luar' = pengurus luar.
     */
    public function up(): void
    {
        Schema::table('pengurus', function (Blueprint $table) {
            $table->enum('asal', ['dalam', 'luar'])->default('dalam')->after('jabatan_id');
        });
    }

    public function down(): void
    {
        Schema::table('pengurus', function (Blueprint $table) {
            $table->dropColumn('asal');
        });
    }
};
