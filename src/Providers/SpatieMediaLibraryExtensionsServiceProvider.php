<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Mlbrgn\SpatieMediaLibraryExtensions\View\Components\MediaManagerSingle;

class SpatieMediaLibraryExtensionsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
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
        Blade::component('media-library-extensions::media-manager-single', MediaManagerSingle::class);

        // blade directives
        Blade::directive('mediaClass', function ($expression) {
            return "<?php echo mle_media_class({$expression}); ?>";
        });

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
