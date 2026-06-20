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
    private const ADMIN_DEFAULT_ABILITIES = ['admin', '*'];

    private const ADMIN_ALLOWED_ABILITIES = [
        'admin',
        '*',
        'packages:read',
        'packages:write',
        'bookings:read',
        'bookings:write',
        'payments:read',
        'payments:write',
        'tokens:manage',
    ];

    private const TOURIST_DEFAULT_ABILITIES = [
        'tourist',
        'bookings:read',
        'bookings:write',
        'payments:read',
        'payments:write',
    ];

    private const TOURIST_ALLOWED_ABILITIES = [
        'tourist',
        'bookings:read',
        'bookings:write',
        'payments:read',
        'payments:write',
    ];

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

        if (! $user) {
            if (Admin::where('email', $validated['email'])->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'This email belongs to an admin account. Please use the Admin Login token request.',
                ]);
            }

            throw ValidationException::withMessages([
                'email' => 'No account exists for this email address. Please create an account first.',
            ]);
        }

        if (! $user->isTourist()) {
            throw ValidationException::withMessages([
                'email' => 'This email belongs to an admin account. Please use the Admin Login token request.',
            ]);
        }

        if (! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'The password you entered is incorrect.',
            ]);
        }

        if ($user->isGuest()) {
            throw ValidationException::withMessages([
                'email' => 'Only registered tourist accounts can create tourist API tokens.',
            ]);
        }

        return $this->issueTokenResponse(
            $user,
            $validated['token_name'] ?? 'Tourist API Token',
            $this->normalizeAbilities($validated['abilities'] ?? null, 'tourist')
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

        if (! $admin) {
            $user = User::where('email', $validated['email'])->first();

            if ($user && $user->isTourist()) {
                throw ValidationException::withMessages([
                    'email' => 'This email belongs to a tourist account. Please use the Tourist Login token request.',
                ]);
            }

            throw ValidationException::withMessages([
                'email' => 'No account exists for this email address. Please create an account first.',
            ]);
        }

        if (($admin->role ?? 'admin') !== 'admin') {
            throw ValidationException::withMessages([
                'email' => 'This email belongs to a tourist account. Please use the Tourist Login token request.',
            ]);
        }

        if (! Hash::check($validated['password'], $admin->password)) {
            throw ValidationException::withMessages([
                'password' => 'The password you entered is incorrect.',
            ]);
        }

        return $this->issueTokenResponse(
            $admin,
            $validated['token_name'] ?? 'Admin API Token',
            $this->normalizeAbilities($validated['abilities'] ?? null, 'admin')
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

        $role = $user instanceof Admin ? 'admin' : ($user->role ?? 'tourist');

        return $this->issueTokenResponse(
            $user,
            $validated['token_name'],
            $this->normalizeAbilities($validated['abilities'] ?? null, $role)
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
            'token_id' => $token->accessToken->id,
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

    private function normalizeAbilities(?array $abilities, string $role): array
    {
        $defaults = $role === 'admin'
            ? self::ADMIN_DEFAULT_ABILITIES
            : self::TOURIST_DEFAULT_ABILITIES;

        if (! $abilities) {
            return $defaults;
        }

        $abilities = array_values(array_unique(array_filter(array_map(
            fn ($ability) => trim((string) $ability),
            $abilities
        ))));

        if (! $abilities) {
            return $defaults;
        }

        $allowed = $role === 'admin'
            ? self::ADMIN_ALLOWED_ABILITIES
            : self::TOURIST_ALLOWED_ABILITIES;

        $invalid = array_values(array_diff($abilities, $allowed));

        if ($invalid) {
            throw ValidationException::withMessages([
                'abilities' => 'Invalid abilities for '.$role.' token: '.implode(', ', $invalid),
            ]);
        }

        return $abilities;
    }
}
