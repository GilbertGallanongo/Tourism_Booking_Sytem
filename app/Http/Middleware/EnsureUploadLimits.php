<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUploadLimits
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Set generous limits for file uploads on web requests (1GB).
        @ini_set('upload_max_filesize', '1024M');
        @ini_set('post_max_size', '1024M');
        @ini_set('memory_limit', '1024M');

        return $next($request);
    }
}
