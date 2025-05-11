<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Providers;

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
            __DIR__.'/../../resources/js/' => public_path('js/vendor/media-library-extensions'),
        ], 'js');

        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }

    public function register(): void
    {

        $this->mergeConfigFrom(__DIR__.'/../../config/media-library-extensions.php', 'media-library-extensions');

        // Bind services or config
    }
}
