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
        return $this->authenticate($request, 'tourist', route('home'));
    }

    public function loginAdmin(Request $request): JsonResponse|RedirectResponse
    {
        return $this->authenticateAdmin($request, route('admin.dashboard'));
    }

    private function authenticate(
        Request $request,
        string $role,
        string $redirectTo
    ): JsonResponse|RedirectResponse {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('email', $credentials['email'])
            ->first();

        if (
            ! $user
            || $user->role !== $role
            || ! $this->passwordMatches($credentials['password'], $user->password)
        ) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Logged in successfully.',
                'user' => $user,
            ]);
        }

        return redirect()->intended($redirectTo);
    }

    private function authenticateAdmin(Request $request, string $redirectTo): JsonResponse|RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('email', $credentials['email'])
            ->where('role', 'admin')
            ->first();

        if ($user && $this->passwordMatches($credentials['password'], $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Logged in successfully.',
                    'user' => $user,
                ]);
            }

            return redirect()->intended($redirectTo);
        }

        $admin = Admin::query()
            ->where('email', $credentials['email'])
            ->first();

        if (
            ! $admin
            || ! $this->passwordMatches($credentials['password'], $admin->password)
        ) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        Auth::guard('admin')->login($admin, $request->boolean('remember'));

        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Logged in successfully.',
                'user' => $admin,
            ]);
        }

        return redirect()->intended($redirectTo);
    }

    public function register(Request $request): JsonResponse|RedirectResponse
    {
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
