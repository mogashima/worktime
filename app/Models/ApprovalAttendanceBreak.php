<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalAttendanceBreak extends Model
{
    use HasFactory;

    protected $table = 'approval_attendance_breaks';

    protected $fillable = [
        'approval_attendance_id',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
    ];

    public function approvalAttendance()
    {
        return $this->belongsTo(ApprovalAttendance::class);
    }
}