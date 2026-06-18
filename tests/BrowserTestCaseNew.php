<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Tests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Mlbrgn\LaravelFormComponents\Providers\FormComponentsServiceProvider;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\DemoController;
use Mlbrgn\MediaLibraryExtensions\Http\Middleware\MlbrgnClientTokenMiddleware;
use Mlbrgn\MediaLibraryExtensions\Providers\MediaLibraryExtensionsServiceProvider;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Ufo;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Browser test filesystem layout.
 *
 * tests/
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
 * MediaLibrary writes file
 * ↓
 * tests/Support/storage/media_*
 * ↓
 * /storage/{disk}/{path}
 * ↓
 * response()->file()
 * ↓
 * browser receives actual image
 */
class BrowserTestCaseNew extends Orchestra
{
    protected $baseUrl = 'http://medialibrary-extensions.test';

    protected Blog $testModel;

    protected Ufo $testModelNotExtendingHasMedia;

    protected const TEST_STORAGE_DISKS = [
        'media_demo',
        'media_originals',
        'media_temporary',
    ];

    protected array $fixtures = [
            '512x512_1:1.png',
            '640x360_16:9.png',
            '720x1280_9:16.png',
            '800x600_4:3.png',
            '1080x1080_1:1.png',
            '1280x720_16:9.png',
            '1920x1080_16:9.png',
            '3840x2160_16:9.png',
    ];

    protected array $invalidMimeTypeFixtures = [
        'invalid-config.json',
        'invalid-mime-test.zip',
        'invalid-image.png',
        'invalid-readme.txt',
    ];

    // runs before every test
    protected function setUp(): void
    {
        parent::setUp();

//        Artisan::call('migrate:fresh', [
//            '--database' => 'sqlite',
//        ]);

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
            MediaLibraryExtensionsServiceProvider::class,
        ];

        if (class_exists(FormComponentsServiceProvider::class)) {
            $providers[] = FormComponentsServiceProvider::class;
        }

        return $providers;
    }

    // Configure the Testbench application before booting.
    public function getEnvironmentSetUp($app): void
    {
        $mainDb = __DIR__.'/../../../../database/browser-testing.sqlite';

        if (file_exists($mainDb)) {
            unlink($mainDb);
        }

        touch($mainDb);

        // setup demo db
        $demoDatabasePath = __DIR__.'/Support/demo.sqlite';

        if (file_exists($demoDatabasePath)) {
            unlink($demoDatabasePath);
        }

        touch($demoDatabasePath);

        $app['config']->set('database.connections.media_demo', [
            'driver' => 'sqlite',
            'database' => $demoDatabasePath,
            'prefix' => '',
        ]);

        $app['config']->set('database.connections.sqlite.database', $mainDb);

        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('medialibrary-extensions.demo_pages_enabled', true);
        $app['config']->set('medialibrary-extensions.route_middleware', ['web', MlbrgnClientTokenMiddleware::class]);

        $app['config']->set('logging.default', 'single');
        $app['config']->set('logging.channels.single', [
            'driver' => 'single',
            'path' => $this->getLogDirectory().'/laravel.log',
            'level' => 'debug',
        ]);
        $app['config']->set('session.serialization', 'php');

        // Load media library config (needed for tests that interact with the media library to work)
        $app['config']->set('media-library', require __DIR__.'/config/media-library.php');

        $app['config']->set('media-library.media_model', Media::class);


        foreach (self::TEST_STORAGE_DISKS as $disk) {
            $this->createDirectory(
                $this->getBrowserStorageDirectory($disk)
            );
        }

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Mlbrgn\\MediaLibraryExtensions\\Tests\\Database\\Factories\\'.class_basename($modelName).'Factory';
        });

        View::addLocation(__DIR__.'/Feature/views');

        $this->registerBrowserTestDisks($app);

        // bind the public path to the test/Support/public directory
        $app->bind('path.public', fn () => $this->getFakePublicDirectory());

        $this->registerRoutes();

    }

    public function getFixtureAsFilePath(string $fileName): string
    {
        $path = __DIR__.'/Fixtures/'.$fileName;

        return $path;
    }

    protected function registerBrowserTestDisks(Application $app): void
    {
        foreach (self::TEST_STORAGE_DISKS as $disk) {
            $app['config']->set("filesystems.disks.{$disk}", [
                'driver' => 'local',
                'root' => $this->getBrowserStorageDirectory($disk),
                'url' => "/storage/{$disk}",
                'visibility' => 'public',
            ]);
        }
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
     * resolves to:
     *
     * tests/Support/storage/media_demo/1/image.jpg
     */
    protected function registerRoutes(): void {

        Route::get('/storage/{disk}/{path}', function (string $disk, string $path) {

            abort_unless(
                in_array($disk, self::TEST_STORAGE_DISKS, true),
                404
            );

            $root = realpath(config("filesystems.disks.{$disk}.root"));

            abort_if($root === false, 404);

            $file = realpath($root.'/'.$path);

            if (
                ! $file ||
                ! str_starts_with($file, $root.DIRECTORY_SEPARATOR)
                || ! is_file($file)
            ) {
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
        });
        Route::get('favicon.ico', fn () => '')->name('mlbrgn.mle.favicon');
    }
    protected function defineDatabaseMigrations(): void
    {
        // package migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // test-only migrations
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

//        $this->artisan('migrate', ['--database' => 'testbench'])->run();
        $this->artisan('migrate', [
            '--database' => 'media_demo',
            '--path' => realpath(__DIR__.'/../database/migrations/demo'),
            '--realpath' => true,
        ])->run();
    }

    protected function createDirectory(string $directory): void
    {
//        if (File::isDirectory($directory)) {
//            File::deleteDirectory($directory);
//        }
//        File::makeDirectory($directory, 0755, true);

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

    public function getRandomFixture(): string {
        return $this->getFixtureAsFilePath(
            $this->fixtures[array_rand($this->fixtures)]
        );
    }

    public function getInvalidMimeTypeFixture(): string {
        return $this->getFixtureAsFilePath(
            $this->invalidMimeTypeFixtures[array_rand($this->invalidMimeTypeFixtures)]
        );
    }

}
