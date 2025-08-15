<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'approval_expense_id',
        'title',
        'date',
        'category_code',
        'description',
        'amount',
        'user_id',
    ];

    // ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_code', 'category_code');
    }

    public function approval()
    {
        // expense は approval に属する（nullable なので belongsTo 側で問題なし）
        return $this->belongsTo(ApprovalExpense::class);
    }
}
