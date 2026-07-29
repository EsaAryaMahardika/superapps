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
            $table->dropForeign('User_Pengurus');
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->foreign('username', 'User_Pengurus')
                ->references('nis')->on('pengurus')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }
};
