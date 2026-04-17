<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->foreignId('id_pelanggan')->constrained('pelanggan', 'id_pelanggan');
            $table->foreignId('id_user')->constrained('pengguna', 'id_user');
            $table->date('tgl_sewa');
            $table->date('tgl_jatuh_tempo');
            $table->date('tgl_kembali')->nullable();
            $table->decimal('total_biaya', 12, 2)->default(0);
            $table->decimal('total_denda', 12, 2)->default(0);
            $table->string('status_transaksi', 50)->default('Diproses'); // Diproses / Selesai / Batal
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};