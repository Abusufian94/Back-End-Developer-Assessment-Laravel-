<?php

use App\Exceptions\ApiException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ApiException $e, Request $request) {
            if (app()->environment('testing')) {
                return response()->json([
                    'status' => 'error',
                    'error' => $e->getError(),
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'details' => $e->getDetails(),
                ], $e->getCode());
            }

            Log::error($e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => 'error',
                'error' => $e->getError(),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'details' => $e->getDetails(),
            ], $e->getCode());
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'error' => 'validation_failed',
                'message' => $e->getMessage(),
                'code' => 422,
                'details' => $e->errors(),
            ], 422);
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'error' => 'unauthenticated',
                'message' => $e->getMessage(),
                'code' => 401,
                'details' => [],
            ], 401);
        });
    })->create();