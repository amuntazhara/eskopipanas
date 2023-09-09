<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subscriptions')->insert([
            'nama_paket' => 'Paket 1',
            'limit_domain' => 3,
            'durasi' => 1,
            'telegram' => 0,
            'harga' => 500000,
        ]);
        DB::table('subscriptions')->insert([
            'nama_paket' => 'Paket 2',
            'limit_domain' => 6,
            'durasi' => 1,
            'telegram' => 1,
            'harga' => 1000000,
        ]);
        DB::table('subscriptions')->insert([
            'nama_paket' => 'Paket 3',
            'limit_domain' => 10,
            'durasi' => 1,
            'telegram' => 1,
            'harga' => 1500000,
        ]);
        DB::table('subscriptions')->insert([
            'nama_paket' => 'Paket 4',
            'limit_domain' => 10,
            'durasi' => 3,
            'telegram' => 1,
            'harga' => 3000000,
        ]);
    }
}
