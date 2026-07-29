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
        Schema::table('user', function (Blueprint $table) {
            // custom_username: username pilihan user, boleh null (gunakan NIS jika null)
            $table->string('custom_username')->nullable()->unique()->after('username');
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropUnique(['custom_username']);
            $table->dropColumn('custom_username');
        });
    }
};
