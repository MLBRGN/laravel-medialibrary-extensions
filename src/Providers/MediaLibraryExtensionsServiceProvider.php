<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Mlbrgn\MediaLibraryExtensions\Console\Commands\InstallMediaLibraryExtensions;
use Mlbrgn\MediaLibraryExtensions\Policies\MediaPolicy;
use Mlbrgn\MediaLibraryExtensions\View\Components\Debug;
use Mlbrgn\MediaLibraryExtensions\View\Components\Flash;
use Mlbrgn\MediaLibraryExtensions\View\Components\Icon;
use Mlbrgn\MediaLibraryExtensions\View\Components\ImageResponsive;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerDestroyForm;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerMultiple;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerSingle;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerUploadForm;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaPreviewer;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaPreviewerModal;
use Mlbrgn\MediaLibraryExtensions\View\Components\Modal;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Service provider for the Media Library Extensions package.
 *
 * This provider handles the registration of views, routes, translations, assets,
 * Blade directives, and Blade components required by the media library extensions.
 */
class MediaLibraryExtensionsServiceProvider extends ServiceProvider
{
    //    private string $vendor = 'mlbrgn';

    private string $packageName = 'laravel-medialibrary-extensions';

    private string $packageNameShort = 'mle';

    private string $nameSpace = 'media-library-extensions';

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
                __DIR__.'/../../resources/views' => resource_path('views/vendor/'.$this->nameSpace),
            ], $this->nameSpace.'-views');

            $this->publishes([
                __DIR__.'/../../dist' => public_path('vendor/'.$this->nameSpace),
            ], $this->nameSpace.'-assets');

            $this->publishes([
                __DIR__.'/../../lang' => resource_path('lang/vendor/'.$this->nameSpace),
            ], $this->nameSpace.'-translations');

            // Publish assets (not working) empty CSS and JS files
            //            $this->publishes([
            //                __DIR__.'/../../resources/assets' => public_path($this->packageName.'/assets'),
            //            ], 'assets');

            $this->publishes([
                __DIR__.'/../../stubs/MediaPolicy.stub' => app_path('Policies/MediaPolicy.php'),
            ], $this->nameSpace.'-policy');

        }
        // register and expose blade views and classes
        Blade::component($this->packageNameShort.'-media-manager-single', MediaManagerSingle::class);
        Blade::component($this->packageNameShort.'-media-manager-multiple', MediaManagerMultiple::class);
        Blade::component($this->packageNameShort.'-media-previewer-modal', MediaPreviewerModal::class);
        Blade::component($this->packageNameShort.'-image-responsive', ImageResponsive::class);
        Blade::component($this->packageNameShort.'-media-previewer', MediaPreviewer::class);

        // register blade views and classes for internal use
        // TODO i don't know how to hide them from the host applications yet (not expose them)
        $this->loadViewComponentsAs($this->packageNameShort.'_internal', [
            Debug::class,
            Icon::class,
            Modal::class,
            Flash::class,
            MediaManagerUploadForm::class,
            MediaManagerDestroyForm::class,
        ]);

        // register policies
        $this->registerPolicy();

    }

    public function register(): void
    {
        parent::register();

        // TODO name is now medialibrary-extension
        $this->mergeConfigFrom(__DIR__.'/../../config/media-library-extensions.php', 'media-library-extensions');
    }

    protected function registerPolicy(): void
    {
        $appPolicy = 'App\\Policies\\MediaPolicy';
        $appPolicyPath = app_path('Policies/MediaPolicy.php');

        if (file_exists($appPolicyPath) && class_exists($appPolicy)) {
            // Host app has published and defined the policy class
            Gate::policy(Media::class, $appPolicy);
        } else {
            // Use package’s fallback policy
            Gate::policy(Media::class, MediaPolicy::class);
        }
    }
}
