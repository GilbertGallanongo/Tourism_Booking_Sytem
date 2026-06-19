<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function showTouristLoginForm(): View
    {
        return view('auth.login', ['role' => 'tourist']);
    }

    public function showAdminLoginForm(): View
    {
        return view('auth.admin-login');
    }

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function loginTourist(Request $request): JsonResponse|RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'token' => ['required', 'string', 'max:255'],
        ]);

        $remember = $request->boolean('remember');

        // Check if user account exists
        $user = User::where('email', $credentials['email'])->first();
        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'No user account found with this email address.',
            ]);
        }

        if (! $this->passwordMatches($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (! $user || $user->role !== 'tourist') {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $this->validateAccountToken($credentials['token'], $user);

        Auth::guard('web')->login($user, $remember);
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Logged in successfully.',
                'user' => $user,
            ]);
        }

        return redirect()->intended(route('home'));
    }

    public function loginAdmin(Request $request): JsonResponse|RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'token' => ['required', 'string', 'max:255'],
        ]);

        $remember = $request->boolean('remember');

        // Check if admin account exists
        $admin = Admin::where('email', $credentials['email'])->first();
        if (! $admin) {
            throw ValidationException::withMessages([
                'email' => 'No admin account found with this email address.',
            ]);
        }

        if (! $this->passwordMatches($credentials['password'], $admin->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (! $admin || $admin->role !== 'admin') {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $this->validateAccountToken($credentials['token'], $admin);

        Auth::guard('admin')->login($admin, $remember);
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Logged in successfully.',
                'user' => $admin,
            ]);
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function loginWithToken(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:255'],
        ]);

        $plainTextToken = trim($validated['token']);
        $accessToken = PersonalAccessToken::findToken($plainTextToken);

        if (! $accessToken || ! $accessToken->tokenable) {
            throw ValidationException::withMessages([
                'token' => 'Invalid access token. Please paste a valid personal access token.',
            ]);
        }

        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'token' => 'This access token has expired. Please create a new token.',
            ]);
        }

        $account = $accessToken->tokenable;
        $remember = $request->boolean('remember');

        if ($account instanceof Admin && ($account->role ?? 'admin') === 'admin') {
            Auth::guard('admin')->login($account, $remember);
            $redirectRoute = 'admin.dashboard';
        } elseif ($account instanceof User && $account->isTourist() && ! $account->isGuest()) {
            Auth::guard('web')->login($account, $remember);
            $redirectRoute = 'home';
        } else {
            throw ValidationException::withMessages([
                'token' => 'This access token does not belong to a valid website account.',
            ]);
        }

        $accessToken->forceFill(['last_used_at' => now()])->save();
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Logged in with access token successfully.',
                'user' => $account,
            ]);
        }

        return redirect()->intended(route($redirectRoute));
    }

    public function register(Request $request): JsonResponse|RedirectResponse
    {
        $currentUser = $request->user();

        if ($currentUser && ! $currentUser->isGuest()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You are already signed in.',
                ], 409);
            }

            return redirect()->route('home')
                ->with('error', 'You are already signed in. Please log out before creating another account.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'tourist',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Account created successfully.',
                'user' => $user,
            ], 201);
        }

        return redirect()->route('home');
    }

    public function guestLogin(Request $request): JsonResponse|RedirectResponse
    {
        $user = User::updateOrCreate(
            ['email' => 'guest@example.com'],
            [
                'name' => 'Guest User',
                'password' => Hash::make(Str::random(32)),
                'role' => 'tourist',
            ]
        );

        Auth::login($user);
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Logged in as guest.',
                'user' => $user,
            ]);
        }

        return redirect()->route('home');
    }

    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::guard('admin')->user() ?? $request->user();
        Auth::guard('admin')->logout();
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Logged out successfully.']);
        }

        return redirect()->route('home');
    }

    private function passwordMatches(string $plainPassword, ?string $storedPassword): bool
    {
        if ($storedPassword === null || $storedPassword === '') {
            return false;
        }

        if (hash_equals($storedPassword, $plainPassword)) {
            return true;
        }

        if ($this->isHashedPassword($storedPassword)) {
            return Hash::check($plainPassword, $storedPassword);
        }

        return false;
    }

    private function validateAccountToken(string $plainTextToken, User|Admin $account): void
    {
        $accessToken = PersonalAccessToken::findToken(trim($plainTextToken));

        if (! $accessToken || ! $accessToken->tokenable || ! $accessToken->tokenable->is($account)) {
            throw ValidationException::withMessages([
                'token' => 'Please enter a valid personal access token for this account.',
            ]);
        }

        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'token' => 'This access token has expired. Please create a new token.',
            ]);
        }

        $accessToken->forceFill(['last_used_at' => now()])->save();
    }

    private function isHashedPassword(string $password): bool
    {
        return Str::startsWith($password, [
            '$2y$',
            '$2a$',
            '$2b$',
            '$argon2i$',
            '$argon2id$',
        ]);
    }
}
