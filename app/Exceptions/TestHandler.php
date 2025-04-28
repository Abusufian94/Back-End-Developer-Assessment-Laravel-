<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TestHandler extends Handler
{
    /**
     * Report or log an exception.
     *
     * @param \Throwable $e
     * @return void
     */
    public function report(\Throwable $e)
    {
        // Skip logging ApiException in testing environment
        if ($e instanceof ApiException) {
            return;
        }

        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param \Throwable $e
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, \Throwable $e)
    {
        if ($e instanceof ApiException) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getError(),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'details' => $e->getDetails(),
            ], $e->getCode());
        }

        if ($e instanceof ValidationException) {
            return response()->json([
                'status' => 'error',
                'error' => 'validation_failed',
                'message' => $e->getMessage(),
                'code' => 422,
                'details' => $e->errors(),
            ], 422);
        }

        if ($e instanceof AuthenticationException) {
            return response()->json([
                'status' => 'error',
                'error' => 'unauthenticated',
                'message' => $e->getMessage(),
                'code' => 401,
                'details' => [],
            ], 401);
        }

        return parent::render($request, $e);
    }
}