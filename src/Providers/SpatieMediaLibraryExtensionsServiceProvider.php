<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Mlbrgn\SpatieMediaLibraryExtensions\Console\Commands\InstallMediaLibraryExtensions;
use Mlbrgn\SpatieMediaLibraryExtensions\Policies\MediaPolicy;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\Debug;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\Flash;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\Icon;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\ImageResponsive;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\MediaManagerMultiple;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\MediaManagerPreviewModal;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\MediaManagerSingle;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\MediaPreviewCarousel;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\Modal;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Service provider for the Media Library Extensions package.
 *
 * This provider handles the registration of views, routes, translations, assets,
 * Blade directives, and Blade components required by the media library extensions.
 */
class SpatieMediaLibraryExtensionsServiceProvider extends ServiceProvider
{
    private string $vendor = 'mlbrgn';

    private string $packageName = 'media-library-extensions';

    private string $nameSpace = 'media-library-extensions';

    private string $packageNameShort = 'mle';

    public function boot(): void
    {

        if (! Schema::hasTable('media')) {
            Log::warning('['.$this->packageName.'] The "media" table is missing. Did you run the Spatie Media Library migration?');
        }

        // This tells Laravel where to find Blade view files
        $this->loadViewsFrom(__DIR__.'/../../resources/views', $this->nameSpace);

        // This tells Laravel where to find the route files
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        // This tells Laravel where to find the translation files
        $this->loadTranslationsFrom(__DIR__.'/../../lang', $this->nameSpace);

        if ($this->app->runningInConsole()) {

            $this->commands([
                InstallMediaLibraryExtensions::class,
            ]);
            $this->publishes([
                __DIR__.'/../../resources/views' => resource_path('views/vendor/'.$this->packageName),
            ], 'views');

            //            $this->publishes([
            //                __DIR__.'/../../dist/js/mediaPreviewModal.js' => public_path('vendor/mlbrgn/js/mediaPreviewModal.js'),
            //                __DIR__.'/../../dist/css/preview-modal.css' => public_path('vendor/mlbrgn/css/preview-modal.css'),
            //            ], 'mlbrgn-assets');

            $this->publishes([
                __DIR__.'/../../lang' => resource_path('lang/vendor/'.$this->packageName),
            ], 'translations');

            // Publish assets (not working) empty css and js files
            //            $this->publishes([
            //                __DIR__.'/../../resources/assets' => public_path($this->packageName.'/assets'),
            //            ], 'assets');

            $this->publishes([
                __DIR__.'/../../stubs/MediaPolicy.stub' => app_path('Policies/MediaPolicy.php'),
            ], 'policy');

        }
        // register and expose blade views and classes
        Blade::component('mle-media-manager-single', MediaManagerSingle::class);
        Blade::component('mle-media-manager-multiple', MediaManagerMultiple::class);
        Blade::component('mle-media-manager-preview-modal', MediaManagerPreviewModal::class);
        Blade::component('mle-image-responsive', ImageResponsive::class);

        // register blade views and classes for internal use
        // TODO i don't know how to hide them from the host applications yet (not expose them)
        $this->loadViewComponentsAs($this->packageNameShort.'_internal', [
            Debug::class,
            Icon::class,
            Modal::class,
            MediaPreviewCarousel::class,
            Flash::class,
        ]);

        // blade directives
        Blade::directive('mediaClass', function ($expression) {
            return "<?php echo mle_media_class($expression); ?>";
        });

        // register policies
        $this->registerPolicy();

    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/media-library-extensions.php', 'media-library-extensions');
    }

    protected function registerPolicy()
    {
        // If the host app has defined its own MediaPolicy, use it
        if (class_exists($appPolicy = 'App\\Policies\\MediaPolicy')) {
            Gate::policy(Media::class, $appPolicy);
        } else {
            Gate::policy(Media::class, MediaPolicy::class);
        }
    }
}
