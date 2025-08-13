<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * 指定ユーザーの勤怠一覧を取得
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $attendances = Attendance::with([
            'attendanceBreaks' => function ($query) {
                $query->orderBy('start_time', 'desc');
            }
        ])->where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->get();

        return $this->responseJson($attendances);
    }

    /**
     * 新規勤怠登録
     */
    public function store(Request $request)
    {
        $userId = Auth::user()->id;
        $validated = $request->validate([
            'date' => 'required|date',
            'clock_in' => 'required|string',
            'clock_out' => 'required|string',
        ]);

        $attendance = Attendance::create([
            'user_id' => $userId,
            'date' => $validated['date'],
            'clock_in' => $validated['clock_in'],
            'clock_out' => $validated['clock_out'],
        ]);

        // 休憩データ作成（リレーション経由）
        if (!empty($request->attendance_breaks)) {
            foreach ($request->attendance_breaks as $break) {
                AttendanceBreak::create([
                    'attendance_id' => $attendance->id,
                    'start_time' => $break['start_time'],
                    'end_time' => $break['end_time'],
                ]);
            }
        }

        // リレーションをロードして返す
        $attendance->load('attendanceBreaks');

        return $this->responseJson($attendance, 201);
    }

    // 勤務開始
    public function startWork(Request $request)
    {
        $user = Auth::user();

        // すでに今日の勤務開始があるかチェック
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        if ($todayAttendance) {
            return $this->responseJson(['message' => '既に勤務開始済みです。'], 400);
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => now()->toTimeString(),
        ]);

        return $this->responseJson($attendance, 201);
    }

    // 勤務終了
    public function endWork(Request $request)
    {
        $user = Auth::user();

        // 今日の勤務記録を取得
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        if (!$attendance) {
            return $this->responseJson(['message' => '勤務開始が見つかりません。'], 404);
        }

        if ($attendance->clock_out) {
            return $this->responseJson(['message' => '既に勤務終了済みです。'], 400);
        }

        $attendance->clock_out = now()->toTimeString();
        $attendance->save();

        return $this->responseJson($attendance);
    }

    // 現在
    public function getCurrent(Request $request)
    {
        $user = Auth::user();

        // 今日の日付 (年月日だけ)
        $today = now()->toDateString();

        // 今日の勤務レコードを取得（あれば1件）
        $attendance = Attendance::with([
            'attendanceBreaks' => function ($query) {
                // 休憩を開始時間の降順で取得
                $query->orderBy('start_time', 'desc');
            }
        ])
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            return $this->responseJson(null);  // 今日の勤務がなければnullなどを返す
        }

        return $this->responseJson($attendance);
    }

}