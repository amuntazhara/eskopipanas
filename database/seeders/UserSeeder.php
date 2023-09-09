<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin',
            'password' => Hash::make('admin'),
            'nama_bo' => '1',
        ]);
        DB::table('users')->insert([
            'name' => 'Stars77',
            'email' => 'stars77@gmail.com',
            'password' => Hash::make('password'),
            'nama_bo' => '2',
        ]);
        DB::table('users')->insert([
            'name' => 'BO Lain',
            'email' => 'bolain@gmail.com',
            'password' => Hash::make('password'),
            'nama_bo' => '3',
        ]);
    }
}
