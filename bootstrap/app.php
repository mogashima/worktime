<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\ApiNotFoundException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // APIエラー用
        $exceptions->render(function (ApiNotFoundException $e) {
            return response()->json([
                'error' => 'Not Found',
                'message' => $e->getMessage()
            ], 404);
        });
        // バリデーションエラー用
        $exceptions->render(function (ValidationException $e) {
            $message = [
                'message' => '入力値に問題があります',
                'errors' => $e->errors()
            ];
            return response()->json($message, 422, [], JSON_UNESCAPED_UNICODE);
        });
    })->create();
