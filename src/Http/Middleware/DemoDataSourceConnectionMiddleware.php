<?php

namespace Mlbrgn\MediaLibraryExtensions\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

class DemoDataSourceConnectionMiddleware
{
    public function handle($request, Closure $next)
    {
        // Only act on demo pages; if demo pages disabled, no-op
        if (! Config::get('medialibrary-extensions.demo_pages_enabled')) {
            return $next($request);
        }

        // Accept data_source via POST body, query string, or route param; default to 'default'
        $dataSource = (string) ($request->input('data_source')
            ?: $request->query('data_source')
            ?: optional($request->route())->parameter('data_source')
            ?: 'default');

        try {
            /** @var DataSourceResolver $resolver */
            $resolver = app(DataSourceResolver::class);
            $connection = $resolver->resolveConnection($dataSource);
        } catch (\Throwable $e) {
            Log::warning('DemoDataSourceConnectionMiddleware: failed to resolve connection for data source; falling back to current default', [
                'data_source' => $dataSource,
                'error' => $e->getMessage(),
            ]);

            return $next($request);
        }

        $currentDefault = Config::get('database.default');

        if ($connection && $connection !== $currentDefault) {
            Log::info('DemoDataSourceConnectionMiddleware: switching default DB connection for demo request', [
                'from' => $currentDefault,
                'to' => $connection,
                'data_source' => $dataSource,
                'route' => optional($request->route())->getName(),
                'url' => $request->fullUrl(),
            ]);

            // Switch default connection for this request lifecycle at both config and manager level
            Config::set('database.default', $connection);
            DB::setDefaultConnection($connection);

            // Ensure a clean slate when flipping sources within the same process
            try {
                // Purge the previous default (if any) to close PDO and avoid cross-usage
                if ($currentDefault && $currentDefault !== $connection) {
                    DB::purge($currentDefault);
                }
            } catch (\Throwable $e) {
                // ignore purge errors for previous default
            }

            try {
                // Also purge target before reconnect to ensure fresh handle
                DB::purge($connection);
                DB::reconnect($connection);
            } catch (\Throwable $e) {
                Log::error('DemoDataSourceConnectionMiddleware: failed to reconnect to resolved connection', [
                    'connection' => $connection,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $next($request);
    }
}
