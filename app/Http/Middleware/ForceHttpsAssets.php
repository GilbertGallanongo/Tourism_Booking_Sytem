<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsAssets
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        if (app()->environment('production')) {
            $response->headers->set('Content-Security-Policy', 'upgrade-insecure-requests');
        }

        return $response;
    }
}
