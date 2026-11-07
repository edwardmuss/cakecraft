<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiGeneratorController extends Controller
{
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:500',
            'tone' => 'sometimes|in:professional,friendly,urgent,promotional'
        ]);

        // Call DeepSeek API (adjust based on their actual API)
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.deepseek.key'),
            'Content-Type' => 'application/json'
        ])->post('https://api.deepseek.com/v1/chat/completions', [
            'model' => 'deepseek-chat',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "You are an SMS copywriter. Generate a concise SMS message (max 320 chars) based on the user's description. Tone: {$validated['tone']}. Return ONLY the message text without emojis or symbols"
                ],
                [
                    'role' => 'user',
                    'content' => $validated['description']
                ]
            ],
            'max_tokens' => 100,
            'temperature' => 0.7
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'API request failed'], 500);
        }

        return response()->json([
            'message' => trim($response->json('choices.0.message.content'))
        ]);
    }
}
