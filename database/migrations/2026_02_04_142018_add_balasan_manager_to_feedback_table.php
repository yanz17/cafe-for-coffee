<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan nama tabel di sini 'feedback' sesuai tabel Anda
        Schema::table('feedback', function (Blueprint $table) {
            $table->text('balasan_manager')->nullable()->after('komentar');
        });
    }

    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropColumn('balasan_manager');
        });
    }
};