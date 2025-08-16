<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\TimeCalculatorService;

class AttendanceBreak extends Model
{
    protected $table = 'attendance_breaks';

    protected $fillable = [
        'attendance_id',
        'clock_in',
        'clock_out',
        'start_time',
        'end_time',
        'break_value',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'clock_in' => 'datetime:H:i:s',
        'clock_out' => 'datetime:H:i:s',
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
        return $this->end_time !== '';
    }

    /**
     * start_timeとend_timeから休憩時間を計算してbreak_valueにセットする
     * @return void
     */
    public function setBreakValue()
    {
        $this->break_value = TimeCalculatorService::getDiffMinute($this->start_time, $this->end_time);
    }
}
