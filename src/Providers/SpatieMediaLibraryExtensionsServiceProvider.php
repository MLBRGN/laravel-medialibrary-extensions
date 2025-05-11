<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class SpatieMediaLibraryExtensionsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'media-library-extensions');

        // Optionally, publish views to app's resources
        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/media-library-extensions'),
        ], 'views');

        $this->publishes([
            __DIR__.'/../../resources/js/' => resource_path('js/vendor/media-library-extensions'),
        ], 'js');

    }

    public function register(): void
    {
        Log::info('SpatieMediaLibraryExtensionsServiceProvider registered.');

        // Bind services or config
    }
}
