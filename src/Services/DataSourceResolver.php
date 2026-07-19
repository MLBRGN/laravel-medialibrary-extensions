<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use InvalidArgumentException;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;

class DataSourceResolver
{

    public function resolveConnection(string $dataSource): string
    {
        // 1) First honor dynamic mappings from configuration to support test/demo overrides
        //    Example: config('medialibrary-extensions.data_sources.alt_source.connection') => 'alt'
        $configured = config("medialibrary-extensions.data_sources.{$dataSource}.connection");
        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        // 2) Backward-compatible fallbacks for well-known aliases used across the package
        return match ($dataSource) {
            'default'      => config('database.default'),
            'demo_default' => PackageInfrastructure::connection('demo', 'default'),
            'demo_alt'     => PackageInfrastructure::connection('demo', 'alt'),
            'test_default' => PackageInfrastructure::connection('test', 'default'),
            'test_alt'     => PackageInfrastructure::connection('test', 'alt'),

            default => throw new InvalidArgumentException("Invalid data source [$dataSource]"),
        };
    }
}
