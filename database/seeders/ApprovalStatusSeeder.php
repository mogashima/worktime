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
            ['status_code' => 'pending', 'name' => '承認待ち'],
            ['status_code' => 'approved', 'name' => '承認済み'],
            ['status_code' => 'rejected', 'name' => '却下'],
        ]);
    }
}
