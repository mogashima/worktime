<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExpenseCategory::insert([
            ['name' => '交通費', 'category_code' => 'travel'],
            ['name' => '宿泊費', 'category_code' => 'hotel'],
            ['name' => '食事代', 'category_code' => 'meal'],
            ['name' => '備品購入', 'category_code' => 'supply'],
            ['name' => '雑費', 'category_code' => 'misc'],
        ]);
    }
}
