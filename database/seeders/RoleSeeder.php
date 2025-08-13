<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            ['role_code' => 'admin', 'name' => '管理者', 'created_at' => now(), 'updated_at' => now()],
            ['role_code' => 'user', 'name' => '一般ユーザー', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
