<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Tests;

use BladeUI\Icons\BladeIconsServiceProvider;
use Davidhsianturi\BladeBootstrapIcons\BladeBootstrapIconsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Mlbrgn\LaravelFormComponents\Providers\FormComponentsServiceProvider;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\DemoController;
use Mlbrgn\MediaLibraryExtensions\Http\Middleware\MlbrgnClientTokenMiddleware;
use Mlbrgn\MediaLibraryExtensions\Interfaces\YouTubeThumbnailDownloader;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
use Mlbrgn\MediaLibraryExtensions\Providers\MediaLibraryExtensionsServiceProvider;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;
use Mlbrgn\MediaLibraryExtensions\Tests\Fakes\FakeYouTubeThumbnailDownloader;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Ufo;
use Mlbrgn\MediaLibraryExtensions\Tests\Support\Http\Controllers\BlogController;
use Mlbrgn\MediaLibraryExtensions\Tests\Support\Http\Controllers\BlogShowcaseController;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

/**
 * Browser test filesystem layout.
 *
 * Tests/
 * └── Support/
 *     └── storage/
 *         ├── media_demo/ TODO check
 *         ├── media_originals/
 *         └── media_temporary/
 *
 * Files are served through a dedicated test route:
 *
 * /storage/media_demo/* // TODO check
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
    protected $baseUrl = 'http://127.0.0.1';

    protected string $infrastructureProfile = 'demo';

    protected Blog $testModel;

    protected Ufo $testModelNotExtendingHasMedia;

    protected float $waitTimeXhr = 0.1;// @AI DO NOT CHANGE!

    protected float $waitTimeNonXhr = 0.5;// @AI DO NOT CHANGE!

    // large files cause timeouts in browser testing, disabled (for now)
    protected array $fixturesSmall = [
        '01_100x100.jpg',
        '02_150x150.jpg',
        '03_200x200.jpg',
        '04_320x240.jpg',
        '05_480x320.jpg',
    ];

    protected array $fixturesMedium = [
        '06_640x480.jpg',
        '07_800x600.jpg',
        '08_1024x768.jpg',
        '09_1280x720.jpg',
        '10_1280x1024.jpg',
    ];

    protected array $fixturesLarge = [
        '11_1920x1080.jpg',
        '12_2560x1440.jpg',
        '13_3840x2160.jpg',
        '14_4096x2160.jpg',
        '15_3840x2160.jpg',
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

    protected static bool $migrated = false;

    // runs before every test
    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateDatabases();
        $this->truncateDatabases();
        $this->seedDatabases();

        Artisan::call('vendor:publish', [
            '--tag' => 'medialibrary-extensions-assets',
            '--force' => true,
        ]);

        date_default_timezone_set('UTC');
        config(['app.timezone' => 'UTC']);

        Carbon::setTestNow('2025-01-01 00:00:00');

        $this->createDirectory(storage_path('framework/sessions'));
        $this->createDirectory(storage_path('logs'));

        $this->testModel = Blog::create(['title' => 'Test Model']);
        $this->testModelNotExtendingHasMedia = Ufo::create(['title' => 'Test Model']);
        $this->app['translator']->addNamespace(
            'medialibrary-extensions',
            __DIR__.'/../lang'
        );

        Route::get('/login', fn () => 'Login (dummy)')->name('login');

        Config::set('medialibrary-extensions.demo_pages_enabled', false);
        Config::set('medialibrary-extensions.debug', true);
        Config::set('medialibrary-extensions.store_originals', true);

        if (empty(config('app.key'))) {
            $key = 'base64:'.base64_encode(random_bytes(32));
            Config::set('app.key', $key);
        }

        $this->app->bind(
            YouTubeThumbnailDownloader::class,
            FakeYouTubeThumbnailDownloader::class
        );

        $this->afterApplicationCreated(function () {
            //            $this->overrideVendorRoutes();
        });
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
        Relation::morphMap([
            'blog' => Blog::class,
            'alien' => Alien::class,
        ]);

        $storagePath = __DIR__.'/Support/storage';
        $app->useStoragePath($storagePath);

        $app['config']->set('medialibrary-extensions.demo_pages_enabled', true);
        $app['config']->set('medialibrary-extensions.min_image_width', 100);
        $app['config']->set('medialibrary-extensions.min_image_height', 100);
        $app['config']->set('medialibrary-extensions.max_image_width', 5000);
        $app['config']->set('medialibrary-extensions.max_image_height', 5000);

        // mark that we are running browser tests to allow safe demo/testing fallbacks
        $app['config']->set('medialibrary-extensions.browser_tests', true);

        PackageInfrastructure::register($this->infrastructureProfile);

        $app['config']->set('medialibrary-extensions.models.blog', Blog::class);
        $app['config']->set('medialibrary-extensions.models.alien', Alien::class);

        $app['config']->set('filesystems.disks.media_originals.root', $storagePath . '/media_originals');
        $app['config']->set('filesystems.disks.media_temporary.root', $storagePath . '/media_temporary');

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

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Mlbrgn\\MediaLibraryExtensions\\Tests\\Database\\Factories\\'.class_basename($modelName).'Factory';
        });

        View::addLocation(__DIR__.'/Feature/views');

        Blade::component('blogs.layout', 'blogs-layout');

        // bind the public path to the test/Support/public directory
        // TODO getFakePublicDirectory() is not a method
        $app->bind('path.public', fn () => $this->getFakePublicDirectory());
        $this->registerRoutes();

    }

    public function getFixtureAsFilePath(string $fileName): string
    {
        $demoPath = __DIR__.'/Fixtures/demo_images/'.$fileName;
        if (file_exists($demoPath)) {
            return $demoPath;
        }

        return __DIR__.'/Fixtures/'.$fileName;
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
            $diskConfig = config("filesystems.disks.$disk");

            if ($diskConfig === null) {
                abort(404);
            }

            $root = realpath($diskConfig['root']);

            if ($root === false) {
                abort(404);
            }

            $file = $root.'/'.$path;

            if (! file_exists($file)) {
                abort(404);
            }

            $mimeType = match (pathinfo($file, PATHINFO_EXTENSION)) {
                'webp' => 'image/webp',
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                default => mime_content_type($file),
            };

            return response()->file($file, [
                'Content-Type' => $mimeType,
                'Access-Control-Allow-Origin' => '*',
            ]);
        })->where('path', '.*');

        Route::middleware('web')->group(function () {
            Route::get('mle-demo', [DemoController::class, 'index'])->name('mle-demo');
            Route::post('mle-demo-alien', [DemoController::class, 'store'])->name('store-alien');

            Route::get('blog-showcase', [BlogShowcaseController::class, 'index'])->name('blog-showcase');
            Route::post('blog-showcase-update', [BlogShowcaseController::class, 'update'])->name('blog-showcase-update');

            Route::resource('blogs', BlogController::class);

            Route::post('test-simple-post', function() {
                return response()->json(['status' => 'ok']);
            })->name('test-simple-post');

            Route::get('mle-theme-switch', fn () => redirect()->back())->name('mlbrgn.mle.theme-switch');

            Route::get('/vendor/mlbrgn/{package}/{path}', function ($package, $path) {

                $root = realpath(__DIR__.'/../../../..');

                $map = [
                    'laravel-medialibrary-extensions' => $root.'/packages/mlbrgn/laravel-medialibrary-extensions/dist',

                    'laravel-form-components' => $root.'/packages/mlbrgn/laravel-form-components/dist',
                ];

                abort_unless(isset($map[$package]), 404);

                $basePath = realpath($map[$package]);
                abort_unless($basePath, 404);

                // Normalize requested path
                $relativePath = ltrim($path, '/');

                // Build full path
                $filePath = $basePath.'/'.$relativePath;

                // Resolve real path (THIS is the key security step)
                $realFilePath = realpath($filePath);

                // Block missing files
                abort_unless($realFilePath && file_exists($realFilePath), 404);

                // CRITICAL: ensure that the file is inside the allowed base directory
                abort_unless(str_starts_with($realFilePath, $basePath), 403);

                return response()->file($realFilePath, [
                    'Content-Type' => match (pathinfo($realFilePath, PATHINFO_EXTENSION)) {
                        'js' => 'application/javascript',
                        'css' => 'text/css',
                        default => 'application/octet-stream',
                    },
                ]);

            })->where('path', '.*');
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
        return storage_path('logs');
    }

    public function getBrowserStorageDirectory(string $suffix = ''): string
    {
        return __DIR__.'/Support/storage'
            .($suffix === '' ? '' : '/'.$suffix);
    }

    public function getRandomFixture(): string
    {
        $rand = rand(1, 100);

        if ($rand <= 90) {
            $pool = $this->fixturesSmall;
        } elseif ($rand <= 98) {
            $pool = $this->fixturesMedium;
        } else {
            $pool = $this->fixturesLarge;
        }

        return $this->getFixtureAsFilePath(
            $pool[array_rand($pool)]
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
        if (static::$migrated) {
            return;
        }

        Log::info('BrowserTestCase - migrateDatabases !!!!!!!!!!');
        $this->artisan('migrate:fresh', [
            '--database' => PackageInfrastructure::connection($this->infrastructureProfile, 'default'),
            '--path' => realpath(__DIR__.'/database/migrations'),
            '--realpath' => true,
        ]);

        $this->artisan('migrate:fresh', [
            '--database' => PackageInfrastructure::connection($this->infrastructureProfile, 'alt'),
            '--path' => realpath(__DIR__.'/../database/demo-migrations'),
            '--realpath' => true,
        ]);

        static::$migrated = true;
    }

    protected function truncateDatabases(): void
    {
        foreach ([
                     PackageInfrastructure::connection($this->infrastructureProfile, 'default'),
                     PackageInfrastructure::connection($this->infrastructureProfile, 'alt'),
                 ] as $connection) {

            $db = \DB::connection($connection);

            $driver = $db->getDriverName();

            if ($driver === 'sqlite') {
                $db->statement('PRAGMA foreign_keys = OFF');
            } else {
                $db->statement('SET FOREIGN_KEY_CHECKS=0');
            }

            foreach (['media', 'mle_temporary_uploads', 'blogs', 'aliens'] as $table) {
                if (\Schema::connection($connection)->hasTable($table)) {
                    $db->table($table)->truncate();
                }
            }

            if ($driver === 'sqlite') {
                $db->statement('PRAGMA foreign_keys = ON');
            } else {
                $db->statement('SET FOREIGN_KEY_CHECKS=1');
            }
        }
    }

    protected function seedDatabases(): void
    {
        Alien::on(PackageInfrastructure::connection($this->infrastructureProfile, 'default'))
            ->create([]);

        Alien::on(PackageInfrastructure::connection($this->infrastructureProfile, 'alt'))
            ->create([]);

        Blog::on(PackageInfrastructure::connection($this->infrastructureProfile, 'default'))
            ->create([
                'title' => 'Test Blog Post',
                'content' => 'This is a test blog post content.',
            ]);
    }

    protected function scrollIntoView($page, string $selector): void
    {
        $page->script("
        document.querySelector('$selector')
            ?.scrollIntoView({ block: 'center', inline: 'center' });
    ");
    }

    /*
     * Debugging helpers
     */

    protected function dumpDatabaseTable(string $table, ?string $connection = null): void
    {
        $connection ??= config('database.default');

        dump([
            'connection' => $connection,
            'table' => $table,
            'count' => DB::connection($connection)->table($table)->count(),
            'rows' => DB::connection($connection)->table($table)->get(),
        ]);
    }

    protected function getFakePublicDirectory(): string
    {
        return __DIR__.'/Support/public';
    }

    /**
     * Ensure there is exactly one medium available for the Media Lab preview.
     *
     * Preference order:
     *  - Reuse an existing upload from the Single manager collection ('alien-single-image') if present
     *  - Otherwise append the demo image to the 'alien-media-lab' collection
     *
     * All actions are executed on the correct connection resolved from the given data source.
     */
    protected function ensureLabMedium(string $dataSource): void
    {
        /** @var Alien $model */
        $model = new Alien;

        if ($dataSource !== '') {
            $connection = app(\Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver::class)->resolveConnection($dataSource);
            $model->setConnection($connection);
        }

        /** @var Alien $existingModel */
        $existingModel = $model->newQuery()->with('media')->first();
        if (! $existingModel) {
            $existingModel = $model->newQuery()->create();
        }

        // Resolve disk for the data source, fallback to 'demo_alt'
        $disk = PackageInfrastructure::disk('demo');

        // If Lab already has media, ensure exactly one is present and its file exists on the expected disk.
        $labMedia = $existingModel->getMedia('alien-media-lab');
        if (! $labMedia->isEmpty()) {
            $current = $labMedia->first();

            // When a record exists but its file was cleaned or lives on a different disk, re-create deterministically.
            $fileExists = is_file($current->getPath());
            $onExpectedDisk = method_exists($current, 'disk') ? ($current->disk === $disk) : true;

            if ($fileExists && $onExpectedDisk) {
                return; // Healthy state
            }

            // Self-heal: reset the collection to a single known demo image on the expected disk.
            $existingModel->clearMediaCollection('alien-media-lab');

            $demoImage = __DIR__.'/../resources/demo/demo_small.jpeg';
            if (is_file($demoImage)) {
                $existingModel
                    ->addMedia($demoImage)
                    ->preservingOriginal()
                    ->toMediaCollection('alien-media-lab', $disk);
            }

            $existingModel->load('media');

            return;
        }

        // Prefer reusing a Single upload if available
        $single = $existingModel->getMedia('alien-single-image')->first();

        if ($single && is_file($single->getPath())) {
            $existingModel
                ->addMedia($single->getPath())
                ->preservingOriginal()
                ->toMediaCollection('alien-media-lab', $disk);

            $existingModel->load('media');

            return;
        }

        // fallback
        $demoImage = __DIR__.'/../resources/demo/demo_small.jpeg';

        if (is_file($demoImage)) {
            $existingModel
                ->addMedia($demoImage)
                ->preservingOriginal()
                ->toMediaCollection('alien-media-lab', $disk);

            $existingModel->load('media');
        }
    }
}
