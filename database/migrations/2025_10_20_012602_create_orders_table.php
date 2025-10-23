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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Pelanggan yang pesan (nullable jika Kasir yang input)
            $table->string('nomor_pesanan')->unique();
            $table->integer('total_harga');
            $table->enum('status_pesanan', ['pending', 'diproses', 'siap_ambil', 'selesai', 'dibatalkan'])->default('pending');
            $table->enum('status_pembayaran', ['menunggu', 'lunas', 'gagal'])->default('menunggu');
            $table->enum('tipe_pemesanan', ['dine_in', 'take_away', 'online'])->default('dine_in');
            $table->string('meja')->nullable(); // Untuk Dine-In
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
