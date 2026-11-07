<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Carbon\Carbon;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\ApiResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class CheckApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Token not provided',
            ], 401);
        }

        // Retrieve token from the database
        $apiToken = ApiToken::where('token', $token)->first();
        if (!$apiToken) {
            return response()->json([
                'status' => false,
                'message' =>  'Token not found'
            ], 401);
        }

        // Attempt to decode and verify the token without returning the payload
        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            $branchId = $payload->get('branch_id');
            $userId = $payload->get('user_id');

            $user = User::find($userId);

            // Check user is verified
            if ($user && is_null($user->registration_verified_at)) {

                return response()->json([
                    'status' => false,
                    'message' => 'Please verify your account to continue.'
                ], 401);
            }
            // --- Standard suspension checks ---
            if (!$user->isActive) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is inactive. Please contact support.'
                ], 401);
            }

            if (!$user->branch || !$user->branch->isActive) {
                return response()->json([
                    'status' => false,
                    'message' => 'The associated company account is inactive.'
                ], 401);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid token: ' . $e->getMessage()
            ], 401);
        }

        // Retrieve token from the database
        $apiToken = ApiToken::where('token', $token)
            ->where('branch_id', $branchId)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
            })
            ->first();
        if (!$apiToken) {
            return response()->json([
                'status' => false,
                'message' =>  'Token not found or expired'
            ], 401);
        }

        // try {
        //     // Parse the JWT token and get the payload
        //     $payload = JWTAuth::parseToken()->getPayload();

        //     // Add branch_id and user_id to the request
        //     $request->merge([
        //         'branch_id' => $payload->get('branch_id'),
        //         'user_id' => $payload->get('user_id')
        //     ]);
        // } catch (JWTException $e) {
        //     // Return a 401 Unauthorized response if the token is invalid or expired
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Invalid or expired token'
        //     ], 401);
        // }

        // Continue to the next middleware
        return $next($request);
    }
}
