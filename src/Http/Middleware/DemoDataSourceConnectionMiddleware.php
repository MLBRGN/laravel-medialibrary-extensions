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
        $dataSource = $request->input('data_source')
            ?? $request->query('data_source')
            ?? 'default';

        $target = match ($dataSource) {
            'demo' => PackageInfrastructure::connection(),
            default => PackageInfrastructure::hostConnection(),
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

//class DemoDataSourceConnectionMiddleware
//{
//    public function handle($request, Closure $next)
//    {
//        // 1) Determine data source for this request (query OR form body)
//        $dataSource = $request->input('data_source')
//            ?? $request->query('data_source')
//            ?? 'default'; // default → simulated host app
//
//        // 2) Resolve the correct connection name for the data source
//        $resolver = app(DataSourceResolver::class);
////        $target = $resolver->resolveConnection($dataSource); // 'media_demo_host_app' or 'media_demo'
//        $target = match ($dataSource) {
//            'demo' => DemoEnvironment::connection(),
//            default => DemoEnvironment::hostConnection(),
//        };
//
//        // 3) Mirror your TestCase: set default connection and data source mapping
//        //    so all code (including Spatie Media) uses the same DB.
//        $previous = DB::getDefaultConnection();
//
//        Config::set('database.default', $target);
//
////        return match ($dataSource) {
////            'demo' => DemoEnvironment::connection(),
////            default => DemoEnvironment::hostConnection(),
////        };
////        Config::set('medialibrary-extensions.data_sources.default.connection', 'media_demo_host_app');
////        Config::set('medialibrary-extensions.data_sources.demo.connection', 'media_demo');
//
//        // 4) Ensure we actually switch connections for this request lifetime
//        DB::setDefaultConnection($target);
//
//        // Purge the previously-open default (commonly 'sqlite') to avoid stale handles
//        if ($previous && $previous !== $target) {
//            DB::purge($previous);
//        }
//
//        // (Re)connect the target so models resolved after this point bind to it
//        DB::reconnect($target);
//
//        Log::debug('DemoDBSwitch: default connection switched', [
//            'data_source' => $dataSource,
//            'to'          => $target,
//            'from'        => $previous,
//        ]);
//
//        return $next($request);
//    }
//}
//class DemoDataSourceConnectionMiddleware
//{
//    public function handle($request, Closure $next)
//    {
//        // Only act on demo pages; if demo pages disabled, no-op
//        if (! Config::get('medialibrary-extensions.demo_pages_enabled')) {
//            return $next($request);
//        }
//
//        // Accept data_source via POST body, query string, or route param; default to 'default'
//        $dataSource = (string) ($request->input('data_source')
//            ?: $request->query('data_source')
//            ?: optional($request->route())->parameter('data_source')
//            ?: 'default');
//
//        try {
//            /** @var DataSourceResolver $resolver */
//            $resolver = app(DataSourceResolver::class);
//            $connection = $resolver->resolveConnection($dataSource);
//            Log::info('DemoDataSourceConnectionMiddleware - handle: resolved connection for data source', [
//               'data_source' => $dataSource,
//               'connection' => $connection,
//            ]);
//        } catch (\Throwable $e) {
//            Log::warning('DemoDataSourceConnectionMiddleware: failed to resolve connection for data source; falling back to current default', [
//                'data_source' => $dataSource,
//                'error' => $e->getMessage(),
//            ]);
//
//            return $next($request);
//        }
//
//        $currentDefault = Config::get('database.default');
//
//        if ($connection && $connection !== $currentDefault) {
//            Log::info('DemoDataSourceConnectionMiddleware: switching default DB connection for demo request', [
//                'from' => $currentDefault,
//                'to' => $connection,
//                'data_source' => $dataSource,
//                'route' => optional($request->route())->getName(),
//                'url' => $request->fullUrl(),
//            ]);
//
//            // Switch default connection for this request lifecycle at both config and manager level
//            Config::set('database.default', $connection);
//            DB::setDefaultConnection($connection);
//
//            // Ensure a clean slate when flipping sources within the same process
//            try {
//                // Purge the previous default (if any) to close PDO and avoid cross-usage
//                if ($currentDefault && $currentDefault !== $connection) {
//                    DB::purge($currentDefault);
//                }
//            } catch (\Throwable $e) {
//                // ignore purge errors for previous default
//            }
//
//            try {
//                // Also purge target before reconnect to ensure fresh handle
//                DB::purge($connection);
//                DB::reconnect($connection);
//            } catch (\Throwable $e) {
//                Log::error('DemoDataSourceConnectionMiddleware: failed to reconnect to resolved connection', [
//                    'connection' => $connection,
//                    'error' => $e->getMessage(),
//                ]);
//            }
//        }
//
//        return $next($request);
//    }
//}
