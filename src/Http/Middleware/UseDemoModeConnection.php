<?php

namespace Mlbrgn\MediaLibraryExtensions\Http\Middleware;

use Artisan;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Schema;

class UseDemoModeConnection
{
    public function handle($request, Closure $next)
    {

        $dbPath = realpath(__DIR__ . '/../../../storage/media-library-extensions-demo.sqlite');

        Config::set('database.connections.media_demo', [
            'driver' => 'sqlite',
            'database' => $dbPath,
            'prefix' => '',
        ]);
        Config::set('database.default', 'media_demo');

        \Log::info('DB connections: ', Config::get('database.connections'));
        DB::purge('media_demo');
        DB::reconnect('media_demo');

        if (! Schema::connection('media_demo')->hasTable('aliens')) {
            Artisan::call('migrate', [
                '--database' => 'media_demo',
                '--path' => realpath(__DIR__ . '/../../../database/migrations/demo'),
                '--realpath' => true,
            ]);
        }
        return $next($request);
    }
}
