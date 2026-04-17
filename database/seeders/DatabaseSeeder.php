<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PenggunaSeeder::class,
            PelangganSeeder::class,
            BarangSeeder::class,
            // TransaksiSeeder::class, // nanti setelah transaksi dibuat
        ]);
    }
}