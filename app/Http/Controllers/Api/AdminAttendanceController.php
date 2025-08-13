<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceBreak;

class AdminAttendanceController extends Controller
{
    /**
     * 特定ユーザーの勤怠一覧
     */
    public function index($user_id)
    {
        $attendances = Attendance::with([
            'attendanceBreaks' => function ($query) {
                $query->orderBy('start_time', 'desc');
            }
        ])->where('user_id', $user_id)
            ->orderBy('date', 'desc')
            ->get();
        return $this->responseJson($attendances);
    }

    /**
     * 特定ユーザーの勤怠新規登録
     */
    public function store(Request $request, $user_id)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'clock_in' => ['required', 'date_format:H:i'],
            'clock_out' => ['required', 'date_format:H:i', 'after:clock_in'],
            'attendance_breaks' => ['array'],
            'attendance_breaks.*.start_time' => ['required', 'date_format:H:i'],
            'attendance_breaks.*.end_time' => ['nullable', 'date_format:H:i', 'after:attendance_breaks.*.start_time'],
            'attendance_breaks.*.id' => ['nullable', 'integer'],
        ]);

        $attendance = new Attendance();
        $attendance->user_id = $user_id;
        $attendance->date = $validated['date'];
        $attendance->clock_in = $validated['clock_in'];
        $attendance->clock_out = $validated['clock_out'];
        $attendance->save();

        // 休憩データ作成
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

    /**
     * 特定ユーザーの勤怠更新
     */
    public function update(Request $request, $user_id, $attendance_id)
    {
        $attendance = Attendance::with('attendanceBreaks')->where('user_id', $user_id)
            ->where('id', $attendance_id)
            ->first();
        if (!$attendance) {
            return $this->responseJson(['message' => '勤怠が見つかりません'], 404);
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'clock_in' => ['required', 'date_format:H:i'],
            'clock_out' => ['required', 'date_format:H:i', 'after:clock_in'],
            'attendance_breaks' => ['array'],
            'attendance_breaks.*.start_time' => ['required', 'date_format:H:i'],
            'attendance_breaks.*.end_time' => ['nullable', 'date_format:H:i', 'after:attendance_breaks.*.start_time'],
            'attendance_breaks.*.id' => ['nullable', 'integer'],
        ]);

        // 勤怠本体更新
        $attendance->date = $validated['date'];
        $attendance->clock_in = $validated['clock_in'];
        $attendance->clock_out = $validated['clock_out'];
        $attendance->save();

        // ===== 休憩更新処理 =====
        // １．登録済みの休憩を取得しておく
        $existingBreaks = $attendance->attendanceBreaks()->get()->keyBy('id');

        $requestBreakIds = [];

        // ２．リクエストの休憩ごとに追加 or 更新
        foreach ($request->attendance_breaks as $breakData) {
            if (empty($breakData['id']) || $breakData['id'] == 0) {
                // 新規追加
                AttendanceBreak::create([
                    'attendance_id' => $attendance->id,
                    'start_time' => $breakData['start_time'],
                    'end_time' => $breakData['end_time'] ?? null,
                ]);
            } elseif ($existingBreaks->has($breakData['id'])) {
                // 既存更新
                $break = $existingBreaks->get($breakData['id']);
                $break->start_time = $breakData['start_time'];
                $break->end_time = $breakData['end_time'] ?? null;
                $break->save();
                $requestBreakIds[] = $breakData['id'];
            }
        }

        // ３．リクエストに無い既存休憩は削除
        $toDelete = $existingBreaks->keys()->diff($requestBreakIds);
        AttendanceBreak::whereIn('id', $toDelete)->delete();

        // リレーションをロードして返す
        $attendance->load('attendanceBreaks');

        return $this->responseJson($attendance);
    }

    /**
     * 特定ユーザーの勤怠削除
     */
    public function delete($user_id, $attendance_id)
    {
        $attendance = Attendance::where('user_id', $user_id)
            ->where('id', $attendance_id)
            ->first();
        if (!$attendance) {
            return $this->responseJson(['message' => '勤怠が見つかりません'], 404);
        }

        $attendance->delete();

        return $this->responseJson(['message' => '削除しました']);
    }
}
