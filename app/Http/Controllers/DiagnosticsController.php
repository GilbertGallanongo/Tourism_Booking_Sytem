<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiagnosticsController extends Controller
{
    public function status(Request $request)
    {
        // Only allow when explicitly enabled
        if (env('FORCE_APP_DEBUG', 'false') !== 'true') {
            return response()->json(['error' => 'Diagnostics disabled'], 404);
        }

        $result = [];

        $result['app_env'] = env('APP_ENV');
        $result['app_debug'] = env('APP_DEBUG');

        // DB check
        try {
            DB::connection()->getPdo();
            $result['database'] = ['ok' => true, 'driver' => config('database.default')];
        } catch (\Exception $e) {
            $result['database'] = ['ok' => false, 'error' => $e->getMessage()];
        }

        // Storage writable check
        $storagePath = storage_path('logs');
        $writable = is_writable($storagePath) || (@mkdir($storagePath, 0775, true) && is_writable($storagePath));
        $result['storage'] = ['path' => $storagePath, 'writable' => $writable];

        // Cache driver
        $result['cache_driver'] = config('cache.default');

        $ok = ($result['database']['ok'] ?? false) && $writable;

        return response()->json($result, $ok ? 200 : 500);
    }
}
