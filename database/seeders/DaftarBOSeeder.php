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
            'nama_bo' => 'admin',
            'kontak' => '+85517347740',
            'subscribe' => '2030-12-31 23:59:59',
            'paket_subs' => 99,
        ]);
        DB::table('daftar_bo')->insert([
            'nama_bo' => 'Stars77',
            'kontak' => '+85517347740',
            'subscribe' => '2023-08-23 17:02:24',
            'paket_subs' => 1,
        ]);
        DB::table('daftar_bo')->insert([
            'nama_bo' => 'BO Lain',
            'kontak' => '+85517347740',
            'subscribe' => '2023-07-23 18:24:15',
            'paket_subs' => 1,
        ]);
    }
}
