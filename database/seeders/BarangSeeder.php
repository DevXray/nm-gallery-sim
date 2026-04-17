<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('barang')->insert([
            [
                'nama_barang' => 'Kamera DSLR Canon EOS 1500D',
                'status_barang' => 'Tersedia',
                'harga_sewa' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_barang' => 'Tripod Professional',
                'status_barang' => 'Tersedia',
                'harga_sewa' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_barang' => 'Lampu Studio LED 100W',
                'status_barang' => 'Disewa',
                'harga_sewa' => 75000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_barang' => 'Background Green Screen',
                'status_barang' => 'Tersedia',
                'harga_sewa' => 100000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_barang' => 'Microphone Wireless',
                'status_barang' => 'Tersedia',
                'harga_sewa' => 80000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}