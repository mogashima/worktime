<?php

use Illuminate\Support\Facades\Route;
use App\Exceptions\ApiNotFoundException;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ApprovalAttendanceController;
use App\Http\Controllers\Api\ApprovalExpenseController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AttendanceBreakController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\ExpenseCategoryController;
use App\Http\Controllers\Api\AdminApprovalAttendanceController;
use App\Http\Controllers\Api\AdminApprovalExpenseController;
use App\Http\Controllers\Api\AdminAttendanceController;

//@TODO 勤怠編集の備考を追加

// ------------------------------
// APIルート
// ------------------------------
Route::prefix('api')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        // ユーザー関連
        Route::prefix('user')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::put('/{user_id}', [UserController::class, 'update']);
            Route::delete('/{user_id}', [UserController::class, 'destroy']);
            // 自身の情報取得
            Route::get('/current', [UserController::class, 'currentUser']);
        });


        Route::prefix('user/{user}')->group(function () {
            // 経費関連
            Route::prefix('expense')->group(function () {
                Route::get('/', [ExpenseController::class, 'index']);
                Route::post('/', [ExpenseController::class, 'store']);
                Route::put('/{expense}', [ExpenseController::class, 'update']);
                Route::delete('/{expense}', [ExpenseController::class, 'delete']);

            });
        });

        // 経費のカテゴリ取得
        Route::prefix('expense/category')->group(function () {
            Route::get('/', [ExpenseCategoryController::class, 'index']);
        });


        // 勤怠関連
        Route::prefix('attendance')->group(function () {
            Route::get('/', [AttendanceController::class, 'index']);
            Route::post('/', [AttendanceController::class, 'store']);
            Route::post('start', [AttendanceController::class, 'startWork']);
            Route::post('end', [AttendanceController::class, 'endWork']);
            Route::get('current', [AttendanceController::class, 'getCurrent']);

            Route::prefix('break')->group(function () {
                Route::post('start', [AttendanceBreakController::class, 'startBreak']);
                Route::post('end', [AttendanceBreakController::class, 'endBreak']);
            });
        });

        // 申請関連
        Route::prefix('approval')->group(function () {
            Route::prefix('attendance')->group(function () {
                Route::post('/', [ApprovalAttendanceController::class, 'store']);
                Route::get('/', [ApprovalAttendanceController::class, 'index']);
            });
            Route::prefix('expense')->group(function () {
                Route::post('/', [ApprovalExpenseController::class, 'store']);
                Route::get('/', [ApprovalExpenseController::class, 'index']);
                Route::delete('/{approvalExpense}', [ApprovalExpenseController::class, 'destroy']);
            });
        });

        // 管理者用
        Route::prefix('admin')->group(function () {
            Route::prefix('user/{user_id}')->group(function () {
                Route::get('attendance', [AdminAttendanceController::class, 'index']);
                Route::post('attendance', [AdminAttendanceController::class, 'store']);
                Route::delete('attendance/{attendance_id}', [AdminAttendanceController::class, 'delete']);
                Route::put('attendance/{attendance_id}', [AdminAttendanceController::class, 'update']);
            });

            // 承認関連
            Route::prefix('approval')->group(function () {
                Route::prefix('attendance')->group(function () {
                    Route::get('/', [AdminApprovalAttendanceController::class, 'index']);
                    Route::put('/{approvalAttendance}', [AdminApprovalAttendanceController::class, 'update']);
                });
                Route::prefix('expense')->group(function () {
                    Route::get('/', [AdminApprovalExpenseController::class, 'index']);
                    Route::put('/{approvalExpense}', [AdminApprovalExpenseController::class, 'update']);
                });
            });
        });
    });

    Route::fallback(function () {
        throw new ApiNotFoundException();
    });
});

// ロール関連
Route::get('/api/role', [RoleController::class, 'index']);

// ログイン画面(name指定の必要があるため定義)
Route::get('/login', function () {
    return view('layouts.app');
})->name('login');

// ------------------------------
// Vue SPA用 キャッチオール
// ------------------------------
Route::get('/{any}', function () {
    return view('layouts.app');
})->where('any', '^(?!api).*');
