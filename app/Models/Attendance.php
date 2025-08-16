<?php

namespace App\Models;

use App\Services\TimeCalculatorService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'start_time',
        'end_time',
        'work_value',
    ];
    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime:H:i:s',
        'clock_out' => 'datetime:H:i:s',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceBreaks()
    {
        return $this->hasMany(AttendanceBreak::class);
    }

    /**
     * start_timeとend_timeから労働時間を計算してwork_valueにセットする
     * @return void
     */
    public function setWorkValue()
    {
        $this->work_value = TimeCalculatorService::getDiffMinute($this->start_time, $this->end_time);
    }
}