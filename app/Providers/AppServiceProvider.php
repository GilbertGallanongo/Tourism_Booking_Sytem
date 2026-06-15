<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $forceDebug = env('FORCE_APP_DEBUG', 'false') === 'true';
        $localMode = App::environment('local') || $forceDebug;

        if (! $localMode) {
            URL::forceScheme('https');
        }

        if (! $localMode) {
            return;
        }

        $dbConnection = env('DB_CONNECTION', 'mysql');
        $isSqlite = $dbConnection === 'sqlite';

        if (! $isSqlite) {
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                $sqlitePath = database_path('database.sqlite');
                if (! file_exists($sqlitePath)) {
                    @mkdir(dirname($sqlitePath), 0775, true);
                    @touch($sqlitePath);
                }

                Config::set('database.default', 'sqlite');
                Config::set('database.connections.sqlite.database', $sqlitePath);
                Config::set('session.driver', 'file');
                Config::set('session.connection', null);
            }
        }

        if ($isSqlite) {
            Config::set('session.driver', 'file');
            Config::set('session.connection', null);
        }
    }
}
