<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Mlbrgn\SpatieMediaLibraryExtensions\Policies\MediaPolicy;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\Debug;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\Icon;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\ImageResponsive;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\MediaManagerMultiple;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\MediaManagerPreviewModal;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\MediaManagerSingle;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\Modal;

/**
 * Service provider for the Media Library Extensions package.
 *
 * This provider handles the registration of views, routes, translations, assets,
 * Blade directives, and Blade components required by the media library extensions.
 */
class SpatieMediaLibraryExtensionsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        if (! Schema::hasTable('media')) {
            Log::warning('[MediaLibraryExtensions] The "media" table is missing. Did you run the Spatie Media Library migration?');
        }

        // This tells Laravel where to find Blade view files
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'media-library-extensions');

        // This tells Laravel where to find the route files
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        // This tells Laravel where to find the translation files
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'media-library-extensions');

        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/media-library-extensions'),
        ], 'views');

        $this->publishes([
            __DIR__.'/../../dist/js/mediaPreviewModal.js' => public_path('vendor/mlbrgn/js/mediaPreviewModal.js'),
            __DIR__.'/../../dist/css/preview-modal.css' => public_path('vendor/mlbrgn/css/preview-modal.css'),
        ], 'mlbrgn-assets');

        $this->publishes([
            __DIR__.'/../lang' => resource_path('lang/vendor/your-package'),
        ], 'your-package-translations');

        // register and expose blade views and classes
        Blade::component('mle-media-manager-single', MediaManagerSingle::class);
        Blade::component('mle-media-manager-multiple', MediaManagerMultiple::class);
        Blade::component('mle-media-manager-preview-modal', MediaManagerPreviewModal::class);
        Blade::component('mle-image-responsive', ImageResponsive::class);

        // register blade views and classes for internal use
        // TODO i don't know how to hide them from the host applications yet (not expose them)
        $this->loadViewComponentsAs('mle_internal', [
            Debug::class,
            Icon::class,
            Modal::class,
        ]);

        // blade directives
        Blade::directive('mediaClass', function ($expression) {
            return "<?php echo mle_media_class($expression); ?>";
        });

        //        Gate::policy(Model::class, MediaPolicy::class); // not ideal for all models

        // Only register if Blade Icons is available
        //        if (class_exists(Factory::class)) {
        //            $this->app->make(Factory::class)->add('bi', [
        //                'path' => __DIR__.'/../../vendor/davidhsianturi/blade-bootstrap-icons/resources/svg',
        //                'prefix' => 'bi',
        //            ]);
        //        }

        // Force Laravel to use the correct model for {media}
        //        Route::model('media', Media::class);
        //        Route::model('media', Media::class);

    }

    public function register(): void
    {

        $this->mergeConfigFrom(__DIR__.'/../../config/media-library-extensions.php', 'media-library-extensions');

        // Bind services or config
    }
}
