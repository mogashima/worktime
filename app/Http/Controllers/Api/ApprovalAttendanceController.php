<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApprovalAttendance;
use App\Models\ApprovalAttendanceBreak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalAttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $approvals = ApprovalAttendance::with(['user', 'reviewer', 'approvalStatus'])
            ->orderBy('created_at', 'desc')
            ->whereUserId($user->id)
            ->get();
        return $this->responseJson($approvals);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'date' => ['required', 'date'],
            'clock_in' => ['nullable', 'date_format:H:i'],
            'clock_out' => ['nullable', 'date_format:H:i'],
            'note' => ['nullable', 'string'],
            'attendance_breaks' => ['array'],
            'attendance_breaks.*.start_time' => ['required', 'date_format:H:i'],
            'attendance_breaks.*.end_time' => ['nullable', 'date_format:H:i', 'after:attendance_breaks.*.start_time'],
            'attendance_breaks.*.id' => ['nullable', 'integer'],
        ]);

        $approval = ApprovalAttendance::create([
            'user_id' => $user->id,
            'attendance_id' => $validated['id'],
            'date' => $validated['date'],
            'clock_in' => $validated['clock_in'] ?? null,
            'clock_out' => $validated['clock_out'] ?? null,
            'note' => $validated['note'] ?? null,
        ]);

        // 休憩データ作成
        if (!empty($request->attendance_breaks)) {
            foreach ($request->attendance_breaks as $break) {
                ApprovalAttendanceBreak::create([
                    'approval_attendance_id' => $approval->id,
                    'start_time' => $break['start_time'],
                    'end_time' => $break['end_time'],
                ]);
            }
        }

        // リレーションをロードして返す
        $approval->load('attendanceBreaks');

        return $this->responseJson($approval, 201);
    }

}
