<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Laravel\Reverb\Loggers\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required|string', // Flutter sends 'mobile_app'
        ]);
        Log::info("Login attempt for {$request->email}");

        $user = User::where('email', $request->email)->first();

        // Check if user exists, password is correct, and account is active
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!$user->isActive) {
            return response()->json(['message' => 'Your account has been deactivated.'], 403);
        }

        // Revoke existing tokens for this specific device to prevent buildup
        $user->tokens()->where('name', $request->device_name)->delete();

        // Create a new Sanctum token
        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'message' => 'Login successful'
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();

        // Assuming you are using Spatie Permission package on Laravel
        // We extract all permission names assigned to the user (either directly or via roles)
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ? asset('images/avatars/' . $user->avatar) : null,
            'branch_id' => $user->branch_id,
            'permissions' => $permissions, // <-- This powers Flutter's RBAC!
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
