<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => '管理者',
                'login_id' => 'admin',
                'password' => Hash::make('!admin'),
                'role_code' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '社員A',
                'login_id' => 'user',
                'password' => Hash::make('!user'),
                'role_code' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
