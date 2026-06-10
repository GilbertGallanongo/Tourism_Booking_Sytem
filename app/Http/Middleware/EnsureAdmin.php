<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user() ?? $request->user();

        if (! $user) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('admin.login');
        }

        if ($user->role !== 'admin') {
            return $request->expectsJson()
                ? response()->json(['message' => 'Forbidden.'], 403)
                : redirect()->route('dashboard')->with('error', 'Admin access required.');
        }

        return $next($request);
    }
}
