<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\HandleCsrfExceptions;
use App\Http\Middleware\ForceHttpsAssets;
use App\Http\Middleware\TrustProxies;

$appKey = getenv('APP_KEY') ?: ($_ENV['APP_KEY'] ?? $_SERVER['APP_KEY'] ?? '');

if ($appKey === '') {
    $generatedKey = 'base64:' . base64_encode(random_bytes(32));
    $envPath = dirname(__DIR__) . '/.env';

    if (file_exists($envPath)) {
        $contents = file_get_contents($envPath) ?: '';

        if (preg_match('/^APP_KEY=.*$/m', $contents)) {
            $contents = preg_replace('/^APP_KEY=.*$/m', "APP_KEY={$generatedKey}", $contents);
        } else {
            $contents = rtrim($contents, "\n") . "\nAPP_KEY={$generatedKey}\n";
        }

        file_put_contents($envPath, $contents, LOCK_EX);
    } else {
        file_put_contents($envPath, "APP_KEY={$generatedKey}\n", LOCK_EX);
    }

    putenv("APP_KEY={$generatedKey}");
    $_ENV['APP_KEY'] = $generatedKey;
    $_SERVER['APP_KEY'] = $generatedKey;
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend([
            TrustProxies::class,
            ForceHttpsAssets::class,
        ]);

        $middleware->alias([
            'admin' => EnsureAdmin::class,
            'not.guest' => \App\Http\Middleware\EnsureNotGuest::class,
        ]);

        $middleware->replace(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, HandleCsrfExceptions::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
