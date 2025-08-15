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
use App\Http\Controllers\Api\AdminUserController;
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

        // 自身の情報取得
        Route::get('user', [UserController::class, 'currentUser']);

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

        // 経費関連
        Route::prefix('expense')->group(function () {
            Route::get('/', [ExpenseController::class, 'index']);
            Route::post('/', [ExpenseController::class, 'store']);
            Route::put('/{expense_id}', [ExpenseController::class, 'update']);
            Route::delete('/{expense_id}', [ExpenseController::class, 'delete']);
            Route::prefix('category')->group(function () {
                Route::get('/', [ExpenseCategoryController::class, 'index']);
            });
        });




        // 承認
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

// ------------------------------
// Vue SPA用 キャッチオール
// ------------------------------
Route::get('/{any}', function () {
    return view('layouts.app');
})->where('any', '^(?!api).*');
