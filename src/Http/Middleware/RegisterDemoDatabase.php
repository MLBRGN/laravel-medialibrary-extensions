<?php

namespace Mlbrgn\MediaLibraryExtensions\Http\Middleware;

use Closure;
// use Illuminate\Support\Facades\Config;
// use Illuminate\Support\Facades\DB;
use Mlbrgn\MediaLibraryExtensions\Models\DemoMedia;

class RegisterDemoDatabase
{
    public function handle($request, Closure $next)
    {
        if (config('medialibrary-extensions.demo_pages_enabled')) {
            //            Log::info('Registering demo database for request: ');
            //            app()->instance('mle-demo-mode', true);

            //            $databaseDefault = config('database.default');
            //            $databaseDemo = config('medialibrary-extensions.demo_database_name');

            // Set the default connection to 'school'
            //            DB::setDefaultConnection($databaseDemo);
            //            Config::set('database.default', $databaseDemo);
            //
            //            // Clear previous connection if already resolved
            //            DB::purge($databaseDefault);
            //
            //            // Optional:
            //            DB::reconnect($databaseDemo);

            //            Config::set('database.default', $databaseDemo);

            //            DB::purge($databaseDemo);

            //            DB::setDefaultConnection($databaseDemo);

            //            DB::reconnect($databaseDemo);

            //            config([
            //                'media-library.media_model' => DemoMedia::class,
            //            ]);

            return $next($request);
        }

        return $next($request);
    }
}
