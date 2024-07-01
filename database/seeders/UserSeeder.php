<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Mr. Admin',
            'role_id' => 1,
            'username' => 'admin',
            'phone' => 88063736462,
            'about' => 'This is admin about',
            'image' => 'avater.png',
            'email' => 'admin@example.com',
            'password' => bcrypt('12345678'),
        ]);
 
        DB::table('users')->insert([
           'name' => 'Mr. User',
           'role_id' => 2,
           'username' => 'user',
           'phone' => 8806300000,
           'about' => 'This is user about',
           'image' => 'avater.png',
           'email' => 'user@example.com',
           'password' => bcrypt('12345678'),
       ]);
    }
}
