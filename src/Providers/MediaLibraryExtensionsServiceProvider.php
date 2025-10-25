<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Providers;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Mlbrgn\MediaLibraryExtensions\Console\Commands\InstallMediaLibraryExtensions;
use Mlbrgn\MediaLibraryExtensions\Console\Commands\ToggleRepository;
use Mlbrgn\MediaLibraryExtensions\Models\Media;
use Mlbrgn\MediaLibraryExtensions\Policies\MediaPolicy;
use Mlbrgn\MediaLibraryExtensions\View\Components\Audio;
use Mlbrgn\MediaLibraryExtensions\View\Components\Document;
use Mlbrgn\MediaLibraryExtensions\View\Components\ImageEditorModal;
use Mlbrgn\MediaLibraryExtensions\View\Components\ImageResponsive;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaCarousel;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaFirstAvailable;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaLab;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManager;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerMultiple;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerSingle;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerTinymce;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaModal;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\DestroyForm;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\ImageEditorForm;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\SetAsFirstForm;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Spinner;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Status;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\StatusArea;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\UploadForm;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\YouTubeUploadForm;
use Mlbrgn\MediaLibraryExtensions\View\Components\Preview\MediaPreviewItemEmpty;
use Mlbrgn\MediaLibraryExtensions\View\Components\Preview\MediaPreviewGrid;
use Mlbrgn\MediaLibraryExtensions\View\Components\Preview\MediaPreviews;
use Mlbrgn\MediaLibraryExtensions\View\Components\Preview\MediaPreviewItem;
use Mlbrgn\MediaLibraryExtensions\View\Components\Preview\MediaPreviewMenu;
use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\Assets;
use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\Debug;
use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\DebugButton;
use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\Icon;
use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\LocalPackageIcon;
use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\MediaPreviewContainer;
use Mlbrgn\MediaLibraryExtensions\View\Components\Video;
use Mlbrgn\MediaLibraryExtensions\View\Components\VideoYouTube;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

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

    private string $vendor = 'mlbrgn';

    private string $nameSpace = 'media-library-extensions';

    public function boot(): void
    {

        if (! Schema::hasTable('media')) {
            Log::warning('['.$this->packageName.'] The "media" table is missing. Did you run the Spatie Media Library migration?');
        }

        // This tells Laravel where to find Blade view files (components a registered separately)
        $this->loadViewsFrom(__DIR__.'/../../resources/views', $this->nameSpace);

        // This tells Laravel where to find the route files
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        // This tells Laravel where to find the translation files
        $this->loadTranslationsFrom(__DIR__.'/../../lang', $this->nameSpace);
        //        $this->loadJsonTranslationsFrom(__DIR__.'/../../lang');

        // Migrate database tables necessary for this package to do it's work
        // only migrations in the top folder are loaded, so no need to exclude the demo folder
        // note these will also run when testing
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        }

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
                __DIR__.'/../../dist' => public_path('vendor/'.$this->vendor.'/'.$this->nameSpace), // TODO when is mlbrgn prefix wanted / needed?
            ], $this->nameSpace.'-assets');

            $this->publishes([
                __DIR__.'/../../lang' => resource_path('lang/vendor/'.$this->nameSpace),
            ], $this->nameSpace.'-translations');

            $this->publishes([
                __DIR__.'/../../stubs/MediaPolicy.stub' => app_path('Policies/MediaPolicy.php'),
            ], $this->nameSpace.'-policy');

        }

        // register and expose blade component views and classes
        Blade::component($this->packageNameShort.'-media-manager', MediaManager::class);
        Blade::component($this->packageNameShort.'-media-lab', MediaLab::class);
        Blade::component($this->packageNameShort.'-media-manager-single', MediaManagerSingle::class);
        Blade::component($this->packageNameShort.'-media-manager-multiple', MediaManagerMultiple::class);
//        Blade::component($this->packageNameShort.'-media-manager-preview', MediaManagerPreview::class);
        Blade::component($this->packageNameShort.'-media-manager-tinymce', MediaManagerTinymce::class);
        Blade::component($this->packageNameShort.'-media-modal', MediaModal::class);
        Blade::component($this->packageNameShort.'-image-responsive', ImageResponsive::class);
        Blade::component($this->packageNameShort.'-video-youtube', VideoYouTube::class);
        Blade::component($this->packageNameShort.'-first-available', MediaFirstAvailable::class);
        Blade::component($this->packageNameShort.'-document', Document::class);
        Blade::component($this->packageNameShort.'-audio', Audio::class);
        Blade::component($this->packageNameShort.'-video', Video::class);
        Blade::component($this->packageNameShort.'-media-carousel', MediaCarousel::class);
        Blade::component($this->packageNameShort.'-image-editor-modal', ImageEditorModal::class);

        // preview subdirectory
        Blade::component($this->packageNameShort.'-media-preview-grid', MediaPreviewGrid::class);
        Blade::component($this->packageNameShort.'-media-previews', MediaPreviews::class);
        Blade::component($this->packageNameShort.'-media-preview-item', MediaPreviewItem::class);
        Blade::component($this->packageNameShort.'-media-preview-menu', MediaPreviewMenu::class);
        Blade::component($this->packageNameShort.'-media-preview-item-empty', MediaPreviewItemEmpty::class);
        Blade::component($this->packageNameShort.'-media-preview-item', MediaPreviewItem::class);

        // shared partials shared component views and classes for internal use
        Blade::component($this->packageNameShort.'-shared-debug', Debug::class);
        Blade::component($this->packageNameShort.'-shared-icon', Icon::class);
        Blade::component($this->packageNameShort.'-shared-assets', Assets::class);
        Blade::component($this->packageNameShort.'-shared-media-preview-container', MediaPreviewContainer::class);
        Blade::component($this->packageNameShort.'-shared-local-package-icon', LocalPackageIcon::class);
        Blade::component($this->packageNameShort.'-shared-debug-button', DebugButton::class);

        // partial component views and classes for internal use
        Blade::component($this->packageNameShort.'-partial-upload-form', UploadForm::class);
        Blade::component($this->packageNameShort.'-partial-image-editor-form', ImageEditorForm::class);
        Blade::component($this->packageNameShort.'-partial-youtube-upload-form', YouTubeUploadForm::class);
        Blade::component($this->packageNameShort.'-partial-destroy-form', DestroyForm::class);
        Blade::component($this->packageNameShort.'-partial-set-as-first-form', SetAsFirstForm::class);
        Blade::component($this->packageNameShort.'-partial-status-area', StatusArea::class);
        Blade::component($this->packageNameShort.'-partial-status', Status::class);
        Blade::component($this->packageNameShort.'-partial-spinner', Spinner::class);

        //                dd(Blade::getClassComponentAliases());
        // register policies
        if (config('media-library-extensions.demo_pages_enabled')) {
            // Always register the demo database connection, but the models will only use it
            // if the request is from a demo page (checked in the model's getConnectionName method)
            $this->registerDemoDatabase();
        }

        // only affects routes using {media} and inside this package
        Route::model('media', Media::class);

        $this->registerPolicy();
        $this->addToAbout();

        // Merge your overrides
        $this->overrideFormComponentsConfig();
    }

    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(__DIR__.'/../../config/media-library-extensions.php', 'media-library-extensions');

        // Register package-specific event provider
        $this->app->register(MediaLibraryExtensionsEventServiceProvider::class);

        // Conditionally register the originals disk
        if (config('media-library-extensions.store_originals', true)) {
            $disks = config('media-library-extensions.disks', []);

            foreach ($disks as $disk => $diskConfig) {
                // Only set the disk if the host app hasn't defined it
                if (! config()->has("filesystems.disks.$disk")) {
                    config()->set("filesystems.disks.$disk", $diskConfig);
                }
            }
        }
    }

    public function registerDemoDatabase(): void
    {
        $connectionName = config('media-library-extensions.temp_database_name');
        $databasePath = storage_path('media-library-extensions-demo.sqlite');

        if (! file_exists($databasePath)) {
            touch($databasePath);
        }

        Config::set("database.connections.$connectionName", [
            'driver' => 'sqlite',
            'database' => $databasePath,
            'prefix' => '',
        ]);

        // Purge and reconnect
        DB::purge($connectionName);
        DB::reconnect($connectionName);

        // Run migrations if needed
        if (! Schema::connection($connectionName)->hasTable('aliens')) {

            Artisan::call('migrate', [
                '--database' => $connectionName,
                '--path' => realpath(__DIR__.'/../../database/migrations/demo'),
                '--realpath' => true,
                '--force' => true,
            ]);
        }

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

    protected function overrideFormComponentsConfig(): void
    {
        $extraScripts = config('form-components.html_editor_tinymce_global_config.extra_scripts', []);
        $extraScripts[] = asset('vendor/mlbrgn/media-library-extensions/tinymce-custom-file-picker.js');
        $overrides = [
            'html_editor_tinymce_global_config.file_picker_callback' => 'mleFilePicker',
            'html_editor_tinymce_global_config.extra_scripts' => $extraScripts,
        ];

        foreach ($overrides as $key => $value) {
            config()->set("form-components.$key", $value);
        }
    }
}
