<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
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
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($userId) {
                    $exists = Attendance::whereUserId($userId)
                        ->whereDate('date', $value)
                        ->exists();
                    if ($exists) {
                        $fail(Lang::get('validation.date_exists'));
                    }
                }
            ],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'attendance_breaks' => ['array'],
            'attendance_breaks.*.start_time' => ['required', 'date_format:H:i'],
            'attendance_breaks.*.end_time' => ['required', 'date_format:H:i'],
            'attendance_breaks.*.id' => ['nullable', 'integer'],
        ]);

        $attendance = new Attendance();
        $attendance->user_id = $userId;
        $attendance->date = $validated['date'];
        $attendance->start_time = $validated['start_time'];
        $attendance->end_time = $validated['end_time'];
        $attendance->setWorkValue();
        $attendance->save();


        // 休憩データ作成（リレーション経由）
        if (!empty($validated['attendance_breaks'])) {
            foreach ($validated['attendance_breaks'] as $break) {
                $attendanceBreak = new AttendanceBreak();
                $attendanceBreak->attendance_id = $attendance->id;
                $attendanceBreak->start_time = $break['start_time'];
                $attendanceBreak->end_time = $break['end_time'];
                $attendanceBreak->setBreakValue();
                $attendanceBreak->save();
            }
        }

        // リレーションをロードして返す
        $attendance->load('attendanceBreaks');

        return $this->responseJson($attendance, 201);
    }

    // 勤務開始
    public function startWork(Request $request)
    {
        $userId = Auth::user()->id;
        $now = Carbon::now();

        // すでに今日の勤務開始があるかチェック
        $todayAttendance = Attendance::whereUserId($userId)
            ->whereDate('date', $now->format('Y-m-d'))
            ->first();

        if ($todayAttendance) {
            return $this->responseJson(['message' => '既に勤務開始済みです。'], 400);
        }

        $attendance = Attendance::create([
            'user_id' => $userId,
            'date' => $now->format('Y-m-d'),
            'clock_in' => $now,
            'start_time' => $now->format('H:i'),
        ]);

        return $this->responseJson($attendance, 201);
    }

    // 勤務終了
    public function endWork(Request $request)
    {
        $userId = Auth::user()->id;
        $now = Carbon::now();

        // 今日の勤務記録を取得
        $attendance = Attendance::whereUserId($userId)
            ->whereDate('date', $now->format('Y-m-d'))
            ->firstOrFail();

        if ($attendance->end_time) {
            return $this->responseJson(['message' => '既に勤務終了済みです。'], 400);
        }

        $attendance->clock_out = $now;
        $attendance->end_time = $now->format('H:i');
        $attendance->setWorkValue();
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