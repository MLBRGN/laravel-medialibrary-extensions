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

        if (!Schema::hasTable('media')) {
            Log::warning('[MediaLibraryExtensions] The "media" table is missing. Did you run the Spatie Media Library migration?');
        }

        // Register views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'media-library-extensions');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
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

        // this links blade view components to their accompanying view class
        Blade::component('mle-media-manager-single', MediaManagerSingle::class);
        Blade::component('mle-media-manager-multiple', MediaManagerMultiple::class);
        Blade::component('mle-media-manager-preview-modal', MediaManagerPreviewModal::class);
        Blade::component('mle-modal', Modal::class);
        Blade::component('mle-debug', Debug::class);
        Blade::component('mle-icon', Icon::class);
        Blade::component('mle-image-responsive', ImageResponsive::class);
        //        Blade::component('mle-preview-modal', Modal::class);

        // blade directives
        Blade::directive('mediaClass', function ($expression) {
            return "<?php echo mle_media_class($expression); ?>";
        });

        Gate::policy(Model::class, MediaPolicy::class); // not ideal for all models

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

    //    protected function configureComponents(): void
    //    {
    //        $this->callAfterResolving(BladeCompiler::class, function () {
    //            $this->registerComponent('media-manager-single', MediaManagerSingle::class);
    //            // Register other components here
    //        });
    //    }

    //    protected function registerComponent(string $component, $class): void
    //    {
    //        Blade::component('media-library-extensions::components.'.$component, $class);
    //        //        Blade::component('mlbrgn-form-'.$tagAlias, $class);
    //        //        Blade::component('media-library-extensions::components.'.$component, 'mypackage-'.$component);
    //    }
}
