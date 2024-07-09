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
            'name' => 'seofreedom',
            'email' => 'seofreedom',
            'password' => Hash::make('@Seofreedom123'),
            'nama_bo' => '1',
        ]);
        DB::table('users')->insert([
            'name' => 'Neko4D',
            'email' => 'neko4d@gmail.com',
            'password' => Hash::make('password'),
            'nama_bo' => '2',
        ]);
        DB::table('users')->insert([
            'name' => 'Cabemanis88',
            'email' => 'cabemanis88@gmail.com',
            'password' => Hash::make('password'),
            'nama_bo' => '3',
        ]);
    }
}
