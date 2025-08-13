<?php

use Illuminate\Support\Facades\Route;
use App\Exceptions\ApiNotFoundException;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ApprovalAttendanceController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AttendanceBreakController;
use App\Http\Controllers\Api\AdminApprovalAttendanceController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AdminAttendanceController;

//@TODO ログインしていない場合にapiをたたくとエラーになる

// ------------------------------
// APIルート
// ------------------------------
Route::prefix('api')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {

        // 自身の情報取得
        Route::get('user', [UserController::class, 'currentUser']);

        // 勤怠関連
        Route::get('attendance', [AttendanceController::class, 'index']);
        Route::post('attendance', [AttendanceController::class, 'store']);
        Route::prefix('attendance')->group(function () {
            Route::post('start', [AttendanceController::class, 'startWork']);
            Route::post('end', [AttendanceController::class, 'endWork']);
            Route::get('current', [AttendanceController::class, 'getCurrent']);

            Route::prefix('break')->group(function () {
                Route::post('start', [AttendanceBreakController::class, 'startBreak']);
                Route::post('end', [AttendanceBreakController::class, 'endBreak']);
            });
        });



        // 承認
        Route::prefix('approval')->group(function () {
            Route::post('attendance', [ApprovalAttendanceController::class, 'store']);
            Route::get('attendance', [ApprovalAttendanceController::class, 'index']);
        });

        Route::prefix('admin')->group(function () {
            Route::get('user', [AdminUserController::class, 'index']);
            Route::put('user/{user_id}', [AdminUserController::class, 'update']);
            Route::delete('user/{user_id}', [AdminUserController::class, 'destroy']);
            Route::prefix('user/{user_id}')->group(function () {
                Route::get('attendance', [AdminAttendanceController::class, 'index']);
                Route::post('attendance', [AdminAttendanceController::class, 'store']);
                Route::delete('attendance/{attendance_id}', [AdminAttendanceController::class, 'delete']);
                Route::put('attendance/{attendance_id}', [AdminAttendanceController::class, 'update']);
            });

            Route::prefix('approval')->group(function () {
                Route::get('attendance', [AdminApprovalAttendanceController::class, 'index']);
                Route::put('attendance/{approvalAttendance}', [AdminApprovalAttendanceController::class, 'update']);
            });
        });
    });

    Route::fallback(function () {
        throw new ApiNotFoundException();
    });
});

// ロール関連
Route::get('/api/role', [RoleController::class, 'index']);

// ------------------------------
// Vue SPA用 キャッチオール
// ------------------------------
Route::get('/{any}', function () {
    return view('layouts.app');
})->where('any', '^(?!api).*');
