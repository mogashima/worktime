<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
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
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($user_id) {
                    $exists = Attendance::whereUserId($user_id)
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
        $attendance->user_id = $user_id;
        $attendance->date = $validated['date'];
        $attendance->start_time = $validated['start_time'];
        $attendance->end_time = $validated['end_time'];
        $attendance->setWorkValue();
        $attendance->save();

        // 休憩データ作成
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

    /**
     * 特定ユーザーの勤怠更新
     */
    public function update(Request $request, $user_id, $attendance_id)
    {
        $attendance = Attendance::with('attendanceBreaks')->whereUserId($user_id)
            ->whereId($attendance_id)
            ->firstOrFail();

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'attendance_breaks' => ['array'],
            'attendance_breaks.*.start_time' => ['required', 'date_format:H:i'],
            'attendance_breaks.*.end_time' => ['required', 'date_format:H:i'],
            'attendance_breaks.*.id' => ['nullable', 'integer'],
        ]);

        // 勤怠本体更新
        $attendance->date = $validated['date'];
        $attendance->start_time = $validated['start_time'];
        $attendance->end_time = $validated['end_time'];
        $attendance->setWorkValue();
        $attendance->save();

        // ===== 休憩更新処理 =====
        // １．登録済みの休憩を取得しておく
        $existingBreaks = $attendance->attendanceBreaks()->get()->keyBy('id');

        $requestBreakIds = [];

        // ２．リクエストの休憩ごとに追加 or 更新
        foreach ($request->attendance_breaks as $break) {
            if (empty($break['id']) || $break['id'] == 0) {
                // 新規追加
                $attendanceBreak = new AttendanceBreak();
                $attendanceBreak->attendance_id = $attendance->id;
                $attendanceBreak->start_time = $break['start_time'];
                $attendanceBreak->end_time = $break['end_time'];
                $attendanceBreak->setBreakValue();
                $attendanceBreak->save();
            } elseif ($existingBreaks->has($break['id'])) {
                // 既存更新
                $break = $existingBreaks->get($break['id']);
                $break->start_time = $break['start_time'];
                $break->end_time = $break['end_time'];
                $break->setBreakValue();
                $break->save();
                $requestBreakIds[] = $break['id'];
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
        $attendance = Attendance::whereUserId($user_id)
            ->whereId($attendance_id)
            ->firstOrFail();

        $attendance->delete();

        return $this->responseJson(['message' => '削除しました']);
    }
}
