<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pengguna')->insert([
            [
                'username' => 'owner',
                'password' => Hash::make('owner123'),
                'nama_lengkap' => 'Nurul Aulia',
                'role' => 'Owner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'karyawan1',
                'password' => Hash::make('karyawan123'),
                'nama_lengkap' => 'Ahmad Fadil',
                'role' => 'Karyawan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}