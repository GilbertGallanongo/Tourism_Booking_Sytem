<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TokenController extends Controller
{
    /**
     * Create an API token for the authenticated user.
     * Tourists use the 'web' guard, admins use the 'admin' guard.
     */
    public function createToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token_name' => ['required', 'string', 'max:100'],
        ]);

        $user = auth('sanctum')->user();

        if (!$user) {
            throw ValidationException::withMessages([
                'auth' => 'User not authenticated.',
            ]);
        }

        $token = $user->createToken($validated['token_name']);

        return response()->json([
            'message' => 'API token created successfully.',
            'token' => $token->plainTextToken,
            'token_name' => $validated['token_name'],
        ], 201);
    }

    /**
     * List all API tokens for the authenticated user.
     */
    public function listTokens(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            throw ValidationException::withMessages([
                'auth' => 'User not authenticated.',
            ]);
        }

        $tokens = $user->tokens()->select('id', 'name', 'last_used_at', 'created_at')->get();

        return response()->json([
            'message' => 'API tokens retrieved successfully.',
            'tokens' => $tokens,
        ]);
    }

    /**
     * Revoke/delete an API token.
     */
    public function revokeToken(Request $request, int $tokenId): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            throw ValidationException::withMessages([
                'auth' => 'User not authenticated.',
            ]);
        }

        $token = $user->tokens()->find($tokenId);

        if (!$token) {
            return response()->json([
                'message' => 'Token not found.',
            ], 404);
        }

        $token->delete();

        return response()->json([
            'message' => 'API token revoked successfully.',
        ]);
    }

    /**
     * Revoke all API tokens for the authenticated user.
     */
    public function revokeAllTokens(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            throw ValidationException::withMessages([
                'auth' => 'User not authenticated.',
            ]);
        }

        $user->tokens()->delete();

        return response()->json([
            'message' => 'All API tokens revoked successfully.',
        ]);
    }
}
