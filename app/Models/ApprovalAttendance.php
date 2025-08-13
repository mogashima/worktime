<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalAttendance extends Model
{
    protected $table = 'approval_attendances';

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'note',
        'status_code',
        'reviewer_id',
        'reviewed_at',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime:H:i:s',
        'clock_out' => 'datetime:H:i:s',
        'reviewed_at' => 'datetime',
    ];

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

    /**
     * 承認ステータス
     */
    public function approvalStatus()
{
    return $this->belongsTo(ApprovalStatus::class, 'status_code', 'status_code');
}
}
