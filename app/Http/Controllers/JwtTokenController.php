<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ApiToken;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class JwtTokenController extends Controller
{
    public function createToken(Request $request)
    {
        $request->validate([
            'token_name' => 'required|string|max:255|unique:api_tokens,name,NULL,id,branch_id,' . Auth::user()->branch_id,
            'expiry_time' => 'nullable|integer|min:1',
        ]);

        // Get the logged-in user
        $user = Auth::user();

        // Check if expiry time is set
        $expiryTime = $request->expiry_time ? Carbon::now()->addHours((int)$request->expiry_time) : null;

        // Define custom claims, including `branch_id`
        $customClaims = [
            'email' => $user->email,
            'user_id' => $user->id,
            'branch_id' => $user->branch_id,
        ];

        // if ($expiryTime) {
        //     $customClaims['exp'] = $expiryTime->timestamp;
        // }

        // Create a JWT token with the custom claims
        $token = JWTAuth::claims($customClaims)->fromUser($user);

        // Save token to the database
        ApiToken::create([
            'name' => $request->token_name,
            'token' => $token,
            'branch_id' => $user->branch_id,
            'expires_at' => $expiryTime,
        ]);

        return response()->json([
            'token' => $token,
            'name' => $request->token_name,
            'message' => 'Token generated successfully.',
        ]);
    }

    public function getAllTokens()
    {
        $user = Auth::user();

        // Get all tokens for the user's account
        $tokens = ApiToken::where('branch_id', $user->branch_id)->get();

        return response()->json($tokens);
    }

    public function deleteToken(Request $request, $id)
    {
        // Find the token by ID and account ID
        $token = ApiToken::where('branch_id', Auth::user()->branch_id)
            ->where('id', $id)
            ->firstOrFail();

        // Delete token
        $token->delete();

        return response()->json(['message' => 'Token deleted successfully.']);
    }
}
