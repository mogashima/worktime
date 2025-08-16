<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalAttendance extends Model
{
    protected $table = 'approval_attendances';

    protected $fillable = [
        'attendance_id',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'note',
        'status_code',
        'reviewer_id',
        'reviewed_at',
    ];

    protected $casts = [
        'date' => 'date',
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
    public function attendanceBreaks()
    {
        return $this->hasMany(ApprovalAttendanceBreak::class);
    }

    // 元の Attendance へのリレーション
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
}
