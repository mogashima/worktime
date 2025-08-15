<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalExpense extends Model
{
    protected $fillable = [
        'title',
        'user_id',
        'status_code',
        'reviewer_id',
        'reviewed_at',
    ];

    public function expenses()
    {
        // Approval に紐づく Expense を取得
        return $this->hasMany(Expense::class);
    }

    /**
     * 承認ステータス
     */
    public function approvalStatus()
    {
        return $this->belongsTo(ApprovalStatus::class, 'status_code', 'status_code');
    }

    /**
     * 申請者ユーザー
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 承認者ユーザー
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}