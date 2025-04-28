<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
//use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'error' => 'unauthenticated1234',
                'message' => 'Unauthenticated.',
                'code' => 401,
                'details' => []
            ], 401);
        }

        // Check if authenticated user has the 'admin' role
        if (Auth::user()->hasRole('admin')) {
            return $next($request);
        }

        // Throw unauthorized exception if user is not an admin
        return response()->json([
            'status' => 'error',
            'error' => 'Unauthorized',
            'message' => 'Restrcited! only admin can acess this link',
          
          
        ], 403);
    }
}
