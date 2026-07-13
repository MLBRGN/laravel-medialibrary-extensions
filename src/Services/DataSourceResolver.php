<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use InvalidArgumentException;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;

class DataSourceResolver
{

    public function resolveConnection(string $dataSource): string
    {
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
