<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PelangganSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pelanggan')->insert([
            [
                'nama_pelanggan' => 'Budi Santoso',
                'no_telp' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 10, Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelanggan' => 'Siti Rahayu',
                'no_telp' => '082345678901',
                'alamat' => 'Jl. Sudirman No. 25, Bandung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}