<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login_id' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return $this->responseJson(['message' => '認証に失敗しました'], 401);
        }

        $user = $request->user();

        // 古いトークンは削除（必要に応じて）
        $user->tokens()->delete();

        // 新しいトークンを発行
        $token = $user->createToken('api-token')->plainTextToken;

        return $this->responseJson([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->responseJson(['message' => 'ログアウトしました']);
    }

    public function register(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'login_id' => 'required|string|max:255|unique:users,login_id',
            'password' => 'required|string|min:8|confirmed',
            'role_code' => 'required|string|exists:roles,role_code',
        ]);

        // ユーザー作成
        $user = User::create([
            'name' => $validated['name'],
            'login_id' => $validated['login_id'],
            'password' => Hash::make($validated['password']),
            'role_code' => $validated['role_code'],
        ]);

        // レスポンス
        return $this->responseJson(['message' => '登録成功'], 201);
    }
}
