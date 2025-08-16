<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApprovalStatus;

class ApprovalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        ApprovalStatus::insert([
            ['status_code' => 'pending', 'name' => '未処理'],
            ['status_code' => 'approved', 'name' => '承認済'],
            ['status_code' => 'rejected', 'name' => '却下'],
        ]);
    }
}
