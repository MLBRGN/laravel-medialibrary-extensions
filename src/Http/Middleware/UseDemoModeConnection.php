<?php

namespace Mlbrgn\MediaLibraryExtensions\Http\Middleware;

use Artisan;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Log;
use Schema;

class UseDemoModeConnection
{
    public function handle($request, Closure $next)
    {
        Log::info('Using demo DB middleware for: ' . $request->fullUrl());

        // Define the SQLite database path
        $dbPath = storage_path('media-library-extensions-demo.sqlite');

        // Ensure the SQLite file exists
        if (!file_exists($dbPath)) {
            touch($dbPath);
        }

        // Set up the demo connection
        Config::set('database.connections.media_demo', [
            'driver' => 'sqlite',
            'database' => $dbPath,
            'prefix' => '',
        ]);

        // Set as the default connection (optional — only if you want all queries to use it)
        Config::set('database.default', 'media_demo');

        // Reset and reconnect the connection
        DB::purge('media_demo');
        DB::reconnect('media_demo');

        // Run migrations if not already migrated
        if (!Schema::connection('media_demo')->hasTable('aliens')) {
            Log::info('Running demo migrations...');

            Artisan::call('migrate', [
                '--database' => 'media_demo',
                '--path' => realpath(__DIR__ . '/../../../database/migrations/demo'),
                '--realpath' => true,
                '--force' => true,
            ]);

            Log::info('Demo migrations completed.');
        }

        return $next($request);
    }
}
