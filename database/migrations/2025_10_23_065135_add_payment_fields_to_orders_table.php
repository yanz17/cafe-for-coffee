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
        Schema::table('orders', function (Blueprint $table) {
                // Kolom ini tidak diisi Pelanggan, jadi HARUS nullable.
                $table->integer('amount_paid')->nullable()->after('total_harga');
                $table->integer('change_due')->nullable()->after('amount_paid');
                $table->string('payment_method_final')->nullable()->after('change_due');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'change_due', 'payment_method_final']);
        });
    }
};
