<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnforceJsonHeader
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check for exact `application/json` value in the Accept header
        if (!$request->header('Accept') || $request->header('Accept') !== 'application/json') {
            return response()->json([
                'status' => false,
                'message' => 'The Accept header must be set to application/json for this endpoint.',
            ], 406); // 406 Not Acceptable
        }

        return $next($request);
    }
}
