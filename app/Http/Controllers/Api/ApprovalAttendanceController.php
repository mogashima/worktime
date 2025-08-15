<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ApprovalAttendance;
use App\Models\ApprovalAttendanceBreak;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;

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
        $userId = Auth::user()->id;
        $attendanceId = $request->id;

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($userId, $attendanceId) {
                    $exists = Attendance::where('user_id', $userId)
                        ->whereDate('date', $value)
                        ->where('id', '!=', $attendanceId)
                        ->exists();
                    if ($exists) {
                        $fail(Lang::get('validation.date_exists'));
                    }
                }
            ],
            'clock_in' => ['nullable', 'date_format:H:i'],
            'clock_out' => ['nullable', 'date_format:H:i', 'after:clock_in'],
            'note' => ['nullable', 'string'],
            'attendance_breaks' => ['array'],
            'attendance_breaks.*.start_time' => ['required', 'date_format:H:i'],
            'attendance_breaks.*.end_time' => ['nullable', 'date_format:H:i', 'after:attendance_breaks.*.start_time'],
            'attendance_breaks.*.id' => ['nullable', 'integer'],
        ]);

        $approval = ApprovalAttendance::create([
            'user_id' => $userId,
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
