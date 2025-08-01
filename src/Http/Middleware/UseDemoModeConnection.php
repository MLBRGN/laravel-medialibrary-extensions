<?php

namespace Mlbrgn\MediaLibraryExtensions\Http\Middleware;

use Artisan;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Log;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Schema;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UseDemoModeConnection
{
    public function handle($request, Closure $next)
    {
        Log::info('Using demo DB middleware for: ' . $request->fullUrl());

        $connectionName = config('media-library-extensions.temp_database_name');
        $databasePath = storage_path('media-library-extensions-demo.sqlite');

        if (!file_exists($databasePath)) {
            touch($databasePath);
        }

        Config::set("database.connections.{$connectionName}", [
            'driver' => 'sqlite',
            'database' => $databasePath,
            'prefix' => '',
        ]);

        // Purge and reconnect
        DB::purge($connectionName);
        DB::reconnect($connectionName);

        // Run migrations if needed
        if (!Schema::connection($connectionName)->hasTable('aliens')) {
            Log::info('Running demo migrations...');
            Artisan::call('migrate', [
                '--database' => $connectionName,
                '--path' => realpath(__DIR__ . '/../../../database/migrations/demo'),
                '--realpath' => true,
                '--force' => true,
            ]);
            Log::info('Demo migrations completed.');
        }

        return $next($request);
    }

}
