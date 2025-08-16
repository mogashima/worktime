<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AttendanceBreakController extends Controller
{
    // 休憩開始
    public function startBreak(Request $request)
    {
        $userId = Auth::user()->id;
        $now = Carbon::now();

        // 今日の勤務記録を取得
        $attendance = Attendance::whereUserId($userId)
            ->whereDate('date', $now->format('Y-m-d'))
            ->firstOrFail();

        // 休憩中かチェック（終了していない休憩があればエラー）
        $ongoingBreak = AttendanceBreak::whereAttendanceId($attendance->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($ongoingBreak && !$ongoingBreak->isFinished()) {
            return $this->responseJson(['message' => '既に休憩中です。'], 400);
        }

        $attendanceBreak = AttendanceBreak::create([
            'attendance_id' => $attendance->id,
            'start_time' => $now->format('H:i'),
            'clock_in' => $now,
        ]);

        return $this->responseJson($attendanceBreak, 201);
    }

    // 休憩終了
    public function endBreak(Request $request)
    {
        $userId = Auth::user()->id;
        $now = Carbon::now();

        $attendance = Attendance::whereUserId($userId)
            ->whereDate('date', $now->format('Y-m-d'))
            ->firstOrFail();

        $ongoingBreak = AttendanceBreak::whereAttendanceId($attendance->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($ongoingBreak->isFinished()) {
            return $this->responseJson(['message' => '休憩中ではありません。'], 400);
        }

        $ongoingBreak->clock_out = $now;
        $ongoingBreak->end_time = $now->format('H:i');
        $ongoingBreak->setBreakValue();
        $ongoingBreak->save();

        return $this->responseJson($ongoingBreak);
    }

}