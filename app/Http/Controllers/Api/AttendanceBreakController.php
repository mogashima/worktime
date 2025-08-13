<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AttendanceBreakController extends Controller
{
    // 休憩開始
    public function startBreak(Request $request)
    {
        $user = Auth::user();

        // 今日の勤務記録を取得
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        if (!$attendance) {
            return $this->responseJson(['message' => '勤務開始が見つかりません。'], 404);
        }

        // 休憩中かチェック（終了していない休憩があればエラー）
        $ongoingBreak = AttendanceBreak::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->first();

        if ($ongoingBreak) {
            return $this->responseJson(['message' => '既に休憩中です。'], 400);
        }

        $attendanceBreak = AttendanceBreak::create([
            'attendance_id' => $attendance->id,
            'start_time' => now()->toTimeString(),
        ]);

        return response()->json($attendanceBreak, 201);
    }

    // 休憩終了
    public function endBreak(Request $request)
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        if (!$attendance) {
            return $this->responseJson(['message' => '勤務開始が見つかりません。'], 404);
        }

        $ongoingBreak = AttendanceBreak::where('attendance_id', $attendance->id)
            ->whereNull('end_time')
            ->first();

        if (!$ongoingBreak) {
            return $this->responseJson(['message' => '休憩中ではありません。'], 400);
        }

        $ongoingBreak->end_time = now()->toTimeString();
        $ongoingBreak->save();

        return $this->responseJson($ongoingBreak);
    }

}