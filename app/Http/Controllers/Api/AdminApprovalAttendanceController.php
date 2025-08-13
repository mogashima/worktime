<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApprovalAttendance;
use App\Models\AttendanceBreak;
use Illuminate\Http\Request;

class AdminApprovalAttendanceController extends Controller
{
    public function index()
    {
        $approvals = ApprovalAttendance::with(['user', 'reviewer', 'approvalStatus'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->responseJson($approvals);
    }

    public function update(Request $request, ApprovalAttendance $approvalAttendance)
    {
        $request->validate([
            'status_code' => [
                'required',
                'string',
                'exists:approval_statuses,status_code',
            ],
        ]);
        $approvalAttendance->status_code = $request->status_code;
        $approvalAttendance->save();

        // 承認された場合は既存の Attendance を更新
        if ($approvalAttendance->status_code === 'approved') {
            $attendance = $approvalAttendance->attendance;

            if ($attendance) {
                // 勤怠本体を更新
                $attendance->clock_in = $approvalAttendance->clock_in;
                $attendance->clock_out = $approvalAttendance->clock_out;
                $attendance->save();

                // 既存の AttendanceBreak を削除して新しいものを作成する方法
                $attendance->attendanceBreaks()->delete();
                if (!empty($approvalAttendance->attendanceBreaks)) {
                    foreach ($approvalAttendance->attendanceBreaks as $break) {
                        AttendanceBreak::create([
                            'attendance_id' => $attendance->id,
                            'start_time' => $break->start_time->format('H:i:s'),
                            'end_time' => $break->end_time->format('H:i:s'),
                        ]);
                    }
                }
            }
        }

        return $this->responseJson($approvalAttendance);
    }
}
