<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class SpatieMediaLibraryExtensionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Log::info('SpatieMediaLibraryExtensionsServiceProvider booted.');
        // Publish config, views, migrations, etc.
    }

    public function register()
    {
        Log::info('SpatieMediaLibraryExtensionsServiceProvider registered.');

        // Bind services or config
    }
}
