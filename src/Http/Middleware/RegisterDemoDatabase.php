<?php

namespace Mlbrgn\MediaLibraryExtensions\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RegisterDemoDatabase
{
    public function handle($request, Closure $next)
    {
        $connectionName = config('media-library-extensions.demo_database_name');
        $databasePath = storage_path('media-library-extensions-demo.sqlite');

        Config::set("database.connections.{$connectionName}", [
            'driver' => 'sqlite',
            'database' => $databasePath,
            'prefix' => '',
        ]);

        if (config('media-library-extensions.demo_pages_enabled')) {
            // Override connection on key models
            TemporaryUpload::resolveConnectionUsing(function () use ($connectionName) {
                return $connectionName;
            });

            Media::resolveConnectionUsing(function () use ($connectionName) {
                return $connectionName;
            });
        }

        return $next($request);
    }
}
