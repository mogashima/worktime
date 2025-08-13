<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceBreak extends Model
{
    protected $table = 'attendance_breaks';

    protected $fillable = [
        'attendance_id',
        'start_time',
        'end_time',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
    ];

    /**
     * AttendanceBreakは1つのAttendanceに属する
     */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * 休憩が終了しているか判定
     */
    public function isFinished(): bool
    {
        return $this->end_time !== null;
    }
}
