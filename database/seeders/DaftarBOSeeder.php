<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DaftarBOSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('daftar_bo')->insert([
            'nama_bo' => 'seofreedom',
            'kontak' => '+85517347740',
            'subscribe' => '2030-12-31 23:59:59',
            'paket_subs' => 99,
        ]);
        DB::table('daftar_bo')->insert([
            'nama_bo' => 'Stars77',
            'kontak' => '',
            'subscribe' => '2023-07-23 17:02:24',
            'paket_subs' => 1,
            'id_telegram' => '',
        ]);
        DB::table('daftar_bo')->insert([
            'nama_bo' => 'Cabemanis88',
            'kontak' => '',
            'subscribe' => '2024-07-31 18:24:15',
            'paket_subs' => 1,
            'id_telegram' => '5685451173',
        ]);
    }
}
