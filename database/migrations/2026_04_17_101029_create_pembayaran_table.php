<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->foreignId('id_transaksi')->constrained('transaksi', 'id_transaksi');
            $table->date('tgl_bayar');
            $table->decimal('nominal_bayar', 12, 2);
            $table->string('metode_bayar', 50); // Tunai / Transfer / QRIS
            $table->string('status_bayar', 50)->default('Pending'); // Pending / Lunas / Gagal
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};