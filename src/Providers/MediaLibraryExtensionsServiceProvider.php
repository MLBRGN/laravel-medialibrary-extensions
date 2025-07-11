<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Providers;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Mlbrgn\MediaLibraryExtensions\Console\Commands\InstallMediaLibraryExtensions;
use Mlbrgn\MediaLibraryExtensions\Console\Commands\ToggleRepository;
use Mlbrgn\MediaLibraryExtensions\Policies\MediaPolicy;
use Mlbrgn\MediaLibraryExtensions\View\Components\Document;
use Mlbrgn\MediaLibraryExtensions\View\Components\ImageEditorModal;
use Mlbrgn\MediaLibraryExtensions\View\Components\ImageResponsive;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaCarousel;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManager;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerMultiple;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerPreview;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerSingle;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaModal;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Status;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Assets;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Debug;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\DestroyForm;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Icon;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\SetAsFirstForm;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Spinner;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\StatusArea;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\UploadForm;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\YouTubeUploadForm;
use Mlbrgn\MediaLibraryExtensions\View\Components\VideoYouTube;
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
        //        $this->loadJsonTranslationsFrom(__DIR__.'/../../lang');

        if ($this->app->runningInConsole()) {

            $this->commands([
                InstallMediaLibraryExtensions::class,
                ToggleRepository::class,
            ]);

            $this->publishes([
                __DIR__.'/../../config/media-library-extensions.php' => config_path('media-library-extensions.php'),
            ], $this->nameSpace.'-config');

            $this->publishes([
                __DIR__.'/../../resources/views' => resource_path('views/vendor/'.$this->nameSpace),
            ], $this->nameSpace.'-views');

            $this->publishes([
                __DIR__.'/../../dist' => public_path('vendor/'.$this->nameSpace),
            ], $this->nameSpace.'-assets');

            $this->publishes([
                __DIR__.'/../../lang' => resource_path('lang/vendor/'.$this->nameSpace),
            ], $this->nameSpace.'-translations');

            $this->publishes([
                __DIR__.'/../../stubs/MediaPolicy.stub' => app_path('Policies/MediaPolicy.php'),
            ], $this->nameSpace.'-policy');

        }

        // register and expose blade views and classes
        Blade::component($this->packageNameShort.'-media-manager', MediaManager::class);
        Blade::component($this->packageNameShort.'-media-manager-single', MediaManagerSingle::class);
        Blade::component($this->packageNameShort.'-media-manager-multiple', MediaManagerMultiple::class);
        Blade::component($this->packageNameShort.'-media-manager-preview', MediaManagerPreview::class);
        Blade::component($this->packageNameShort.'-media-modal', MediaModal::class);
        Blade::component($this->packageNameShort.'-image-responsive', ImageResponsive::class);
        Blade::component($this->packageNameShort.'-video-youtube', VideoYouTube::class);
        Blade::component($this->packageNameShort.'-document', Document::class);
        Blade::component($this->packageNameShort.'-media-carousel', MediaCarousel::class);
        Blade::component($this->packageNameShort.'-image-editor-modal', ImageEditorModal::class);

        // partials for internal use
        Blade::component($this->packageNameShort.'-partial-upload-form', UploadForm::class);
        Blade::component($this->packageNameShort.'-partial-youtube-upload-form', YouTubeUploadForm::class);
        Blade::component($this->packageNameShort.'-partial-destroy-form', DestroyForm::class);
        Blade::component($this->packageNameShort.'-partial-set-as-first-form', SetAsFirstForm::class);
        Blade::component($this->packageNameShort.'-partial-debug', Debug::class);
        Blade::component($this->packageNameShort.'-partial-icon', Icon::class);
        Blade::component($this->packageNameShort.'-partial-status-area', StatusArea::class);
        Blade::component($this->packageNameShort.'-partial-status', Status::class);
        Blade::component($this->packageNameShort.'-partial-assets', Assets::class);
        Blade::component($this->packageNameShort.'-partial-spinner', Spinner::class);

        // register policies
        $this->registerPolicy();

        $this->addToAbout();

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

    protected function addToAbout(): void
    {
        AboutCommand::add($this->packageName, function () {
            $composer = json_decode(file_get_contents(__DIR__.'/../../composer.json'), true);

            return [
                'Version' => $composer['version'] ?? 'unknown',
            ];
        });
    }
}
