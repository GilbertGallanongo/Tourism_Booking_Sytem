<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class TokenController extends Controller
{
    public function loginTourist(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'token_name' => ['nullable', 'string', 'max:100'],
            'abilities' => ['nullable', 'array'],
            'abilities.*' => ['string', 'max:100'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (! $user->isTourist() || $user->isGuest()) {
            throw ValidationException::withMessages([
                'email' => 'Only registered tourist accounts can create tourist API tokens.',
            ]);
        }

        return $this->issueTokenResponse(
            $user,
            $validated['token_name'] ?? 'Tourist API Token',
            $validated['abilities'] ?? ['tourist']
        );
    }

    public function loginAdmin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'token_name' => ['nullable', 'string', 'max:100'],
            'abilities' => ['nullable', 'array'],
            'abilities.*' => ['string', 'max:100'],
        ]);

        $admin = Admin::where('email', $validated['email'])->first();

        if (! $admin || ! Hash::check($validated['password'], $admin->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (($admin->role ?? 'admin') !== 'admin') {
            throw ValidationException::withMessages([
                'email' => 'Only admin accounts can create admin API tokens.',
            ]);
        }

        return $this->issueTokenResponse(
            $admin,
            $validated['token_name'] ?? 'Admin API Token',
            $validated['abilities'] ?? ['admin']
        );
    }

    /**
     * Create an API token for the authenticated user.
     * Tourists use the 'web' guard, admins use the 'admin' guard.
     */
    public function createToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token_name' => ['required', 'string', 'max:100'],
            'abilities' => ['nullable', 'array'],
            'abilities.*' => ['string', 'max:100'],
        ]);

        $user = $request->user();

        if (!$user) {
            throw ValidationException::withMessages([
                'auth' => 'User not authenticated.',
            ]);
        }

        if ($user instanceof User && $user->isGuest()) {
            throw ValidationException::withMessages([
                'auth' => 'Guest accounts cannot create API tokens.',
            ]);
        }

        return $this->issueTokenResponse(
            $user,
            $validated['token_name'],
            $validated['abilities'] ?? ['*']
        );
    }

    /**
     * List all API tokens for the authenticated user.
     */
    public function listTokens(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            throw ValidationException::withMessages([
                'auth' => 'User not authenticated.',
            ]);
        }

        $tokens = $user->tokens()
            ->select('id', 'name', 'abilities', 'last_used_at', 'expires_at', 'created_at')
            ->latest()
            ->get();

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
        $user = $request->user();

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
        $user = $request->user();

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

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Current API token revoked successfully.',
        ]);
    }

    private function issueTokenResponse($user, string $tokenName, array $abilities): JsonResponse
    {
        $token = $user->createToken($tokenName, $abilities);

        return response()->json([
            'message' => 'API token created successfully.',
            'token' => $token->plainTextToken,
            'token_name' => $tokenName,
            'abilities' => $abilities,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'admin',
                'type' => $user instanceof Admin ? 'admin' : 'tourist',
            ],
        ], 201);
    }
}
