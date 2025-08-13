<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;

class AdminUserController extends Controller
{
    /**
     * 全ユーザー一覧を取得
     */
    public function index()
    {
        // ユーザーと勤怠データを取得（例: attendances リレーション）
        $users = User::all();
        return $this->responseJson($users);
    }

    /**
     * ユーザー情報の更新
     */
    public function update(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        $user->update($validated);

        return $this->responseJson([
            'message' => 'ユーザー情報を更新しました',
            'user' => $user
        ]);
    }

    /**
     * ユーザー削除
     */
    public function destroy($userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();

        return $this->responseJson(['message' => 'ユーザーを削除しました']);
    }
}
