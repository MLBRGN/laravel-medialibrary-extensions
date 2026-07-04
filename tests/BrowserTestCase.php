<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Tests;

use BladeUI\Icons\BladeIconsServiceProvider;
use Davidhsianturi\BladeBootstrapIcons\BladeBootstrapIconsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Mlbrgn\LaravelFormComponents\Providers\FormComponentsServiceProvider;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\DemoController;
use Mlbrgn\MediaLibraryExtensions\Http\Middleware\MlbrgnClientTokenMiddleware;
use Mlbrgn\MediaLibraryExtensions\Interfaces\YouTubeThumbnailDownloader;
use Mlbrgn\MediaLibraryExtensions\Providers\MediaLibraryExtensionsServiceProvider;
use Mlbrgn\MediaLibraryExtensions\Tests\Fakes\FakeYouTubeThumbnailDownloader;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Ufo;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

/**
 * Browser test filesystem layout.
 *
 * Tests/
 * └── Support/
 *     └── storage/
 *         ├── media_demo/
 *         ├── media_originals/
 *         └── media_temporary/
 *
 * Files are served through a dedicated test route:
 *
 * /storage/media_demo/*
 * /storage/media_originals/*
 * /storage/media_temporary/*
 *
 * This mimics Laravel's public storage URLs without requiring
 * a real web server, public/storage symlink, or host application.
 *
 * Browser test
 * ↓
 * MediaLibrary writes the file
 * ↓
 * tests/Support/storage/media_*
 * ↓
 * /storage/{disk}/{path}
 * ↓
 * response()->file()
 * ↓
 * browser receives actual image
 */
class BrowserTestCase extends Orchestra
{
    protected $baseUrl = 'http://medialibrary-extensions.test';

    protected Blog $testModel;

    protected Ufo $testModelNotExtendingHasMedia;

    protected const TEST_STORAGE_DISKS = [
        'media_demo',
        'media_originals',
        'media_temporary',
    ];

    // large files cause timeouts in browser testing, disabled (for now)
    protected array $fixtures = [
        '512x512_1:1.png',
        '640x360_16:9.png',
        '720x1280_9:16.png',
        '800x600_4:3.png',
    ];

    protected array $invalidMimeTypeFixtures = [
        'invalid-config.json',
        'invalid-mime-test.zip',
        'invalid-image.png',
        'invalid-readme.txt',
    ];

    protected array $tinyImageFixtures = [
        'tiny.jpg',
        'tiny.png',
        'tiny.webp',
    ];

    protected bool $migrated = false;

    // runs before every test
    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateDatabases();

        date_default_timezone_set('UTC');
        config(['app.timezone' => 'UTC']);

        Carbon::setTestNow('2025-01-01 00:00:00');

        $this->testModel = Blog::create(['title' => 'Test Model']);
        $this->testModelNotExtendingHasMedia = Ufo::create(['title' => 'Test Model']);
        $this->app['translator']->addNamespace(
            'medialibrary-extensions',
            __DIR__.'/../lang'
        );

        Route::get('/login', fn () => 'Login (dummy)')->name('login');

        Config::set('medialibrary-extensions.demo_pages_enabled', false);
        Config::set('medialibrary-extensions.store_originals', true);

        if (empty(config('app.key'))) {
            $key = 'base64:'.base64_encode(random_bytes(32));
            Config::set('app.key', $key);
        }

        $this->app->bind(
            YouTubeThumbnailDownloader::class,
            FakeYouTubeThumbnailDownloader::class
        );
    }

    protected function tearDown(): void
    {
        // Reset Carbon's test clock
        Carbon::setTestNow();

        parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        $providers = [
            MediaLibraryServiceProvider::class, // YouTube video download browser testing fails without these
            MediaLibraryExtensionsServiceProvider::class,
            BladeIconsServiceProvider::class,
            BladeBootstrapIconsServiceProvider::class,
        ];

        if (class_exists(FormComponentsServiceProvider::class)) {
            $providers[] = FormComponentsServiceProvider::class;
        }

        return $providers;
    }

    // Configure the Testbench application before booting.
    public function getEnvironmentSetUp($app): void
    {
        $pathToHostAppTestDb = __DIR__.'/database/mle-browser-tests-demo-host-app.sqlite';
        $pathToDemoTestDb = __DIR__.'/database/mle-browser-tests-demo.sqlite';

        // create the database files if they don't exist
        if (! file_exists($pathToHostAppTestDb)) {
            touch($pathToHostAppTestDb);
        }

        if (! file_exists($pathToDemoTestDb)) {
            touch($pathToDemoTestDb);
        }

        // configure the database connections
        $app['config']->set('database.connections.mle_demo_host_app', [
            'driver' => 'sqlite',
            'database' => $pathToHostAppTestDb,
            'prefix' => '',
        ]);

        $app['config']->set('database.connections.mle_demo', [
            'driver' => 'sqlite',
            'database' => $pathToDemoTestDb,
            'prefix' => '',
        ]);

        // set the database connections to use (DataSourceResolver looks in data_sources.xxxx.connection)
        $app['config']->set('database.default', 'mle_demo_host_app');
        $app['config']->set('medialibrary-extensions.data_sources.default.connection', 'mle_demo_host_app');
        $app['config']->set('medialibrary-extensions.data_sources.demo.connection', 'mle_demo');

        // enable demo pages
        $app['config']->set('medialibrary-extensions.demo_pages_enabled', true);

        // TODO needed?
        $app['config']->set('medialibrary-extensions.route_middleware', ['web', MlbrgnClientTokenMiddleware::class]);

        // configure logging
        $app['config']->set('logging.default', 'single');
        $app['config']->set('logging.channels.single', [
            'driver' => 'single',
            'path' => $this->getLogDirectory().'/laravel.log',
            'level' => 'debug',
        ]);

        // configure sessions
        //        'driver' => env('SESSION_DRIVER', 'database'),
        $app['config']->set('session.driver', 'file');
        $app['config']->set('session.serialization', 'php');

        // Load media library config (needed for tests that interact with the media library to work)
        $app['config']->set('media-library', require __DIR__.'/config/media-library.php');

        // set the media model to use
        $app['config']->set('media-library.media_model', Media::class);

        // create the storage directories
        foreach (self::TEST_STORAGE_DISKS as $disk) {
            $this->createDirectory(
                $this->getBrowserStorageDirectory($disk)
            );
        }

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Mlbrgn\\MediaLibraryExtensions\\Tests\\Database\\Factories\\'.class_basename($modelName).'Factory';
        });

        View::addLocation(__DIR__.'/Feature/views');

        foreach (self::TEST_STORAGE_DISKS as $disk) {
            $app['config']->set("filesystems.disks.$disk", [
                'driver' => 'local',
                'root' => $this->getBrowserStorageDirectory($disk),
                'url' => "/storage/$disk",
                'visibility' => 'public',
            ]);
        }

        // bind the public path to the test/Support/public directory
        $app->bind('path.public', fn () => $this->getFakePublicDirectory());

        $this->registerRoutes();

    }

    public function getFixtureAsFilePath(string $fileName): string
    {
        $path = __DIR__.'/Fixtures/'.$fileName;

        return $path;
    }

    /**
     * Browser tests run inside a package, not a full Laravel application.
     *
     * In a normal application, files under public/storage would be served
     * directly by the web server (Nginx/Apache).
     *
     * During package browser tests there is no real public storage layer,
     * so we expose configured filesystem disks through a dedicated route.
     *
     * Example:
     *
     * /storage/media_demo/1/image.jpg
     *
     * Resolves to:
     *
     * tests/Support/storage/media_demo/1/image.jpg
     */
    protected function registerRoutes(): void
    {

        Route::get('/storage/{disk}/{path}', function (string $disk, string $path) {

            Log::info('Storage request', [
                'url' => request()->fullUrl(),
                'referer' => request()->headers->get('referer'),
                'disk' => $disk,
                'path' => $path,
            ]);

            if (! in_array($disk, self::TEST_STORAGE_DISKS, true)) {
                Log::warning('Invalid storage disk requested', [
                    'disk' => $disk,
                    'allowed' => self::TEST_STORAGE_DISKS,
                    'path' => $path,
                ]);

                abort(404);
            }

            $root = realpath(config("filesystems.disks.$disk.root"));

            if ($root === false) {
                Log::warning('Storage root does not exist', [
                    'disk' => $disk,
                    'configured_root' => config("filesystems.disks.$disk.root"),
                ]);

                abort(404);
            }

            $file = realpath($root.'/'.$path);

            if (
                ! $file ||
                ! str_starts_with($file, $root.DIRECTORY_SEPARATOR)
                || ! is_file($file)
            ) {
                Log::warning('Storage file not found', [
                    'disk' => $disk,
                    'root' => $root,
                    'path' => $path,
                    'resolved' => $file,
                ]);
                abort(404);
            }

            return response()->file($file);
        })->where('path', '.*');

        Route::middleware('web')->group(function () {
            Route::get('mle-demo', DemoController::class)->name('mle-demo');
            Route::get('mle-theme-switch', fn () => redirect()->back())->name('mlbrgn.mle.theme-switch');

            Route::get('/vendor/mlbrgn/{package}/{type}/{path}', function ($package, $type, $path) {
                $baseDistPath = realpath(__DIR__.'/../dist');
                $filePath = $baseDistPath.'/'.$type.'/'.$path;

                if (! $baseDistPath || ! str_starts_with(realpath($filePath) ?: '', $baseDistPath)) {
                    abort(403);
                }

                if (! file_exists($filePath)) {
                    abort(404);
                }

                $mimeType = match ($type) {
                    'css' => 'text/css',
                    'js' => 'application/javascript',
                    default => File::mimeType($filePath),
                };

                return response()->file($filePath, [
                    'Content-Type' => $mimeType,
                ]);
            })->where('path', '.*');
            //            Route::get('image-editor-translations/nl.json', function () {
            //                return response()->json([]);
            //            });

        });
        Route::get('image-editor-translations/{locale}.json', function () {
            return response()->json([]);
        });
        Route::get('favicon.ico', fn () => '')->name('mlbrgn.mle.favicon');
    }

    protected function createDirectory(string $directory): void
    {

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    }

    public function getLogDirectory(): string
    {
        return __DIR__.'/.logs';
    }

    public function getBrowserStorageDirectory(string $suffix = ''): string
    {
        return __DIR__.'/Support/storage'
            .($suffix === '' ? '' : '/'.$suffix);
    }

    public function getRandomFixture(): string
    {
        return $this->getFixtureAsFilePath(
            $this->fixtures[array_rand($this->fixtures)]
        );
    }

    public function getYouTubeFixture(): string
    {
        return 'https://www.youtube.com/watch?v=jNQXAC9IVRw';
    }

    public function getInvalidMimeTypeFixture(): string
    {
        return $this->getFixtureAsFilePath(
            $this->invalidMimeTypeFixtures[array_rand($this->invalidMimeTypeFixtures)]
        );
    }

    public function getTinyImageFixture(): string
    {
        return $this->getFixtureAsFilePath(
            $this->tinyImageFixtures[array_rand($this->tinyImageFixtures)]
        );
    }

    protected function migrateDatabases(): void
    {
        static $migrated = false;

        if ($migrated) {
            return;
        }

        $this->artisan('migrate:fresh', [
            '--database' => 'mle_demo_host_app', // connection to use
            '--path' => realpath(__DIR__.'/database/migrations'),
            '--realpath' => true,
        ]);

        $this->artisan('migrate:fresh', [
            '--database' => 'mle_demo', // connection to use
            '--path' => realpath(__DIR__.'/../database/demo-migrations'),
            '--realpath' => true,
        ]);

        $migrated = true;
    }

    protected function scrollIntoView($page, string $selector): void
    {
        $page->script("
        document.querySelector('$selector')
            ?.scrollIntoView({ block: 'center', inline: 'center' });
    ");
    }
}
