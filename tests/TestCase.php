<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Tests;

use Mlbrgn\SpatieMediaLibraryExtensions\Providers\SpatieMediaLibraryExtensionsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            SpatieMediaLibraryExtensionsServiceProvider::class,
        ];
    }
}
