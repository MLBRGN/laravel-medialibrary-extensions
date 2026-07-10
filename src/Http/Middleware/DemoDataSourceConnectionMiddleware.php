<?php

namespace Mlbrgn\MediaLibraryExtensions\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;

class DemoDataSourceConnectionMiddleware
{
    public function handle($request, Closure $next)
    {
        dd('DEPRECATED?');
        $dataSource = $request->input('data_source')
            ?? $request->query('data_source')
            ?? 'default';

        $target = match ($dataSource) {
            'demo' => PackageInfrastructure::connection('demo', 'alt'),
            default => PackageInfrastructure::connection('demo', 'default'),
        };

        $previous = DB::getDefaultConnection();

        Config::set('database.default', $target);
        DB::setDefaultConnection($target);

        if ($previous && $previous !== $target) {
            DB::purge($previous);
        }

        DB::reconnect($target);

        Log::debug('DemoDBSwitch: default connection switched', [
            'data_source' => $dataSource,
            'to' => $target,
            'from' => $previous,
        ]);

        return $next($request);
    }
}
