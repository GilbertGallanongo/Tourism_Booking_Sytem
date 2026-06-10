<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotGuest
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated. Please create a tourist account to book.'], 401)
                : redirect()->route('home')->with('error', 'Please create a tourist account to book.');
        }

        if ($user->isGuest()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Guest accounts cannot book. Please create a tourist account.'], 403)
                : redirect()->route('home')->with('error', 'Guest accounts can browse tours only. Create a tourist account to make a booking.');
        }

        return $next($request);
    }
}
