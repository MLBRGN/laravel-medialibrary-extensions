<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UseDemoModeConnection
{
    public function handle($request, Closure $next)
    {

        // only affects routes using {media} and inside this package
        //        Route::model('media', \Mlbrgn\MediaLibraryExtensions\Models\Media::class);
        //        if (config('media-library-extensions.demo_mode_enabled')) {
        //            // Override connection on key models
        //            \Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload::resolveConnectionUsing(function () {
        //                return 'media_demo';
        //            });
        //
        //            \Spatie\MediaLibrary\MediaCollections\Models\Media::resolveConnectionUsing(function () {
        //                return 'media_demo';
        //            });
        //        }
        //        // Only apply during demo pages
        //        if (config('media-library-extensions.demo_mode_enabled')) {
        // //            Config::set('media-library.media_model', DemoMediaModel::class);
        //            app()->bind(Media::class, function () {
        //                return new \Mlbrgn\MediaLibraryExtensions\Models\Media();
        //            });
        //        }
        //        Log::info('Using demo DB middleware for: ' . $request->fullUrl());

        //        $connectionName = config('media-library-extensions.temp_database_name');
        //        $databasePath = storage_path('media-library-extensions-demo.sqlite');
        //
        //        if (!file_exists($databasePath)) {
        //            touch($databasePath);
        //        }
        //
        //        Config::set("database.connections.{$connectionName}", [
        //            'driver' => 'sqlite',
        //            'database' => $databasePath,
        //            'prefix' => '',
        //        ]);
        //
        //        // Purge and reconnect
        //        DB::purge($connectionName);
        //        DB::reconnect($connectionName);
        //
        //        // Run migrations if needed
        //        if (!Schema::connection($connectionName)->hasTable('aliens')) {
        //            Log::info('Running demo migrations...');
        //            Artisan::call('migrate', [
        //                '--database' => $connectionName,
        //                '--path' => realpath(__DIR__ . '/../../../database/migrations/demo'),
        //                '--realpath' => true,
        //                '--force' => true,
        //            ]);
        //            Log::info('Demo migrations completed.');
        //        }

        return $next($request);
    }
}
