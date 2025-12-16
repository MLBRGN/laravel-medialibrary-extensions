<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Providers\MediaLibraryExtensionsServiceProvider;
use Mlbrgn\MediaLibraryExtensions\Tests\Database\Factories\TemporaryUploadFactory;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Ufo;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\User;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// class TestCase extends BaseTestCase
class TestCase extends Orchestra
{
    protected $baseUrl = 'http://medialibrary-extensions.test';

    protected Blog $testModel;

    protected Ufo $testModelNotExtendingHasMedia;

    // runs before every test
    protected function setUp(): void
    {
        parent::setUp();

        date_default_timezone_set('UTC');
        config(['app.timezone' => 'UTC']);

        // Run package migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Carbon::setTestNow('2025-01-01 00:00:00');

        $this->testModel = Blog::create(['title' => 'Test Model']);
        $this->testModelNotExtendingHasMedia = Ufo::create(['title' => 'Test Model']);
        $this->app['translator']->addNamespace(
            'medialibrary-extensions',
            __DIR__.'/../lang'
        );

        Route::get('/login', fn () => 'Login (dummy)')->name('login');

        Config::set('media-library-extensions.demo_pages_enabled', false);
        Config::set('media-library-extensions.store_originals', true);

        if (empty(config('app.key'))) {
            $key = 'base64:'.base64_encode(random_bytes(32));
            Config::set('app.key', $key);
        }
//
//        $this->app->singleton(IconsManifest::class, function () {
//            return new IconsManifest(storage_path('framework/blade-icons.php'));
//        });
        // Use a persistent session driver
        //        Config::set('session.driver', 'file');
    }

    protected function tearDown(): void
    {
        // Reset Carbon's test clock
        Carbon::setTestNow();

        parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        return [
            MediaLibraryExtensionsServiceProvider::class,
        ];
    }

    public function registerTestRoute($uri, ?callable $post = null): self
    {
        Route::middleware('web')->group(function () use ($uri, $post) {
            Route::view($uri, $uri);

            if ($post) {
                Route::post($uri, $post);
            }
        });

        return $this;
    }

    // runs once per test suite boot
    public function getEnvironmentSetUp($app): void
    {

        $this->createDirectory($this->getTempDirectory());
        $this->createDirectory($this->getMediaDirectory());
        $this->createDirectory($this->getTemporaryUploadsDirectory());

        $this->refreshTestFiles();

        $app['config']->set('app.key', 'base64:BOiGLFUC+84Du2o8GYos0kGJaj4zGX9M9BkLsAj04Ik=');
        $app['config']->set('session.serialization', 'php');

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Mlbrgn\\MediaLibraryExtensions\\Tests\\Database\\Factories\\'.class_basename($modelName).'Factory';
        });

        View::addLocation(__DIR__.'/Feature/views');

        // Load media library config (needed for tests that interact with the media library to work)
        $app['config']->set('media-library', require __DIR__.'/config/media-library.php');

        // configure database
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // configure filesystem
        config()->set('filesystems.disks.public', [
            'driver' => 'local',
            'root' => $this->getMediaDirectory(),
            'url' => '/media',
        ]);

        $app->bind('path.public', fn () => $this->getTempDirectory());

        $app['config']->set('media-library.media_model', Media::class);
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        // also loads migrations from the service provider!!!
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->artisan('migrate', ['--database' => 'testbench'])->run();
    }

    //    protected function createDirectory($directory): void
    //    {
    //        if (File::isDirectory($directory)) {
    //            File::deleteDirectory($directory);
    //        }
    //        File::makeDirectory($directory);
    //    }
    protected function createDirectory(string $directory): void
    {
        if (File::isDirectory($directory)) {
            File::deleteDirectory($directory);
        }
        File::makeDirectory($directory, 0755, true);
    }

    public function getTempDirectory(string $suffix = ''): string
    {
        return __DIR__.'/Support/tmp'.($suffix == '' ? '' : '/'.$suffix);
    }

    public function getMediaDirectory(string $suffix = ''): string
    {
        return $this->getTempDirectory('media'.($suffix == '' ? '' : '/'.$suffix));
    }

    private function getTemporaryUploadsDirectory(): string
    {
        return __DIR__.'/Support/tmp/uploads';
    }

    //    public function getFixtureUploadedFile($fileName): string
    //    {
    //        return $this->getTempDirectory('uploads/'.$fileName);
    //    }

    public function getFixtureUploadedFile(string $fileName): UploadedFile
    {
        $path = $this->getTempDirectory('uploads/'.$fileName);

        if (! file_exists($path)) {
            throw new \RuntimeException("Fixture file not found: {$path}");
        }

        return new UploadedFile(
            $path,
            basename($fileName),
            mime_content_type($path) ?: null,
            null,
            true // mark as a test file
        );
    }

    //    public function getFixtureUploadedFile($fileName): UploadedFile
    //    {
    //        dump('getFixtureUploadedFile '. $fileName);
    //        $path = $this->getTempDirectory('uploads/'.$fileName);
    //
    //        dump($path);
    //        try {
    //            $mimeType = mime_content_type($path);
    //        } catch (\Exception $e) {
    //            dump('failed to get mime type');
    //            dump($fileName);
    //            $mimeType = 'not found';
    //        }
    //        return new UploadedFile(
    //            path: $path,
    //            originalName: basename($fileName),
    //            mimeType: $mimeType,
    //            error: null,
    //            test: true, // mark as test upload (so no real upload validation)
    //        );
    //    }

    protected function getUploadedFile(
        string $name = 'test.jpg',
        int $sizeInKb = 10,
        string $mimeType = 'image/jpeg'
    ): UploadedFile {
        // Ensure the local test disk exists
        Storage::fake('local');

        // Create a temporary file
        $path = sys_get_temp_dir().'/'.$name;
        file_put_contents($path, str_repeat('a', $sizeInKb * 1024));

        return new UploadedFile(
            $path,
            $name,
            $mimeType,
            null,
            true // mark it as a test file
        );
    }

    protected function refreshTestFiles(): void
    {
        // reset uploads dir
        $this->createDirectory($this->getTemporaryUploadsDirectory());

        // copy all fixture images into uploads dir
        File::copyDirectory(__DIR__.'/Support/files', $this->getTemporaryUploadsDirectory());
    }

    protected function createTemporaryUpload(array $attributes = [])
    {
        return TemporaryUploadFactory::new()->create($attributes);
    }

    public function getUser(): User
    {
        return new User(['id' => 123, 'name' => 'Test User']);
    }

    public function getTestBlogModel(): Model
    {
        return $this->testModel;
    }

    public function getTestModelNotExtendingHasMedia(): Model
    {
        return $this->testModelNotExtendingHasMedia;
    }

    public function getMediaModel($id = 1): Model
    {
        return new Media([
            'id' => $id,
            'collection_name' => 'blog-images',
            'disk' => 'media',
            'file_name' => 'test.jpg',
            'mime_type' => 'image/jpeg',
            'custom_properties' => [],
        ]);
    }

    public function getTemporaryUpload(string $fileName = 'temp.jpg', array $overrides = []): TemporaryUpload
    {
        $defaults = [
            'disk' => 'media',
            'path' => 'uploads/'.$fileName,
            'name' => pathinfo($fileName, PATHINFO_FILENAME),
            'size' => 1024024,
            'file_name' => $fileName,
            'collection_name' => 'test',
            'custom_properties' => ['image_collection' => 'images'],
            'session_id' => session()->getId(),
        ];

        return TemporaryUpload::create(array_merge($defaults, $overrides));
    }

    public function getTestImagePath(string $fileName = 'test.jpg'): string
    {
        $source = __DIR__.'/Support/files/'.$fileName;
        $target = $this->getTempDirectory('uploads/'.$fileName);

        if (! file_exists($target)) {
            File::ensureDirectoryExists(dirname($target));
            File::copy($source, $target);
        }

        return $target;
    }

    //    public function getTestImagePath(string $fileName = 'test.jpg'): string
    //    {
    //
    //        $source = __DIR__.'/Support/files/'.$fileName;
    // //        dump($source);
    // //        dump($fileName);
    //        $target = $this->getFixtureUploadedFile($fileName);
    //
    //        File::ensureDirectoryExists(dirname($target));
    //        File::copy($source, $target);
    //
    //        return $target;
    //    }

    public function getModelWithMedia(array $types = ['image' => 1]): Model
    {
        $model = $this->getTestBlogModel();
        $pool = [
            'image' => ['test.jpg', 'test2.jpg', 'test3.jpg', 'test.png', 'test2.png', 'test3.png'],
            'video' => ['test.mp4'],
            'audio' => ['test.mp3'],
            'document' => ['test.pdf'],
        ];

        $collectionMap = [
            'image' => 'image_collection',
            'video' => 'video_collection',
            'audio' => 'audio_collection',
            'document' => 'document_collection',
        ];

        $counter = 1; // used for deterministic UUIDs

        foreach ($types as $type => $count) {
            if (! isset($pool[$type])) {
                throw new \InvalidArgumentException("Unsupported media type '{$type}'");
            }

            $files = [];
            for ($i = 0; $i < $count; $i++) {
                $files[] = $pool[$type][$i % count($pool[$type])];
            }

            foreach ($files as $fileName) {
                $source = __DIR__.'/Support/files/'.$fileName;

                if (! File::exists($source)) {
                    throw new \RuntimeException("Test file '{$fileName}' does not exist in Support/files.");
                }

                $target = $this->getFixtureUploadedFile($fileName);
                File::ensureDirectoryExists(dirname($target));

                if (! File::exists($target)) {
                    File::copy($source, $target);
                }

                $media = $model
                    ->addMedia($target)
                    ->preservingOriginal()
                    ->toMediaCollection($collectionMap[$type]);

                //  overwrite UUID for stable snapshots
                $media->uuid = sprintf('00000000-0000-4000-8000-%012d', $counter++);
                $media->save();

            }
        }

        $model = $model->fresh();

        // nullify timestamp otherwise snapshot testing might fail
        $model->timestamps = false;
        $model->update(['created_at' => null, 'updated_at' => null]);

        return $model;
    }

    public function getMediaModelWithMedia(array $types = ['image' => 1]): Media
    {
        $model = $this->getTestBlogModel(); // Temporary parent model for attaching media

        $pool = [
            'image' => ['test.jpg', 'test2.jpg', 'test3.jpg', 'test.png', 'test2.png', 'test3.png'],
            'video' => ['test.mp4'],
            'audio' => ['test.mp3'],
            'document' => ['test.pdf'],
        ];

        $collectionMap = [
            'image' => 'image_collection',
            'video' => 'video_collection',
            'audio' => 'audio_collection',
            'document' => 'document_collection',
        ];

        // Pick the first type from the array
        $type = array_key_first($types);
        $count = $types[$type] ?? 1;

        if (! isset($pool[$type])) {
            throw new \InvalidArgumentException("Unsupported media type '{$type}'");
        }

        // Pick the first file for that type
        $fileName = $pool[$type][0];
        $source = __DIR__.'/Support/files/'.$fileName;

        if (! File::exists($source)) {
            throw new \RuntimeException("Test file '{$fileName}' does not exist in Support/files.");
        }

        $target = $this->getFixtureUploadedFile($fileName);
        File::ensureDirectoryExists(dirname($target));

        if (! File::exists($target)) {
            File::copy($source, $target);
        }

        // Attach and return the Media model
        return $model
            ->addMedia($target)
            ->preservingOriginal()
            ->toMediaCollection($collectionMap[$type]);
    }

    public function getTestFilePath(string $fileName): string
    {
        $source = __DIR__.'/Support/files/'.$fileName;

        if (! File::exists($source)) {
            throw new \RuntimeException("Test file '{$fileName}' does not exist in Support/files.");
        }

        $target = $this->getFixtureUploadedFile($fileName);
        File::ensureDirectoryExists(dirname($target));
        File::copy($source, $target);

        return $target;
    }

    /**
     * Create and return a Media model ready for testing.
     *
     * @param  string|null  $fileName  The filename (must exist in Support/files)
     * @param  string  $collection  Media collection name
     * @param  array  $customProperties  Extra custom_properties
     * @param  array  $generatedConversions  Conversion map (e.g. ['thumb' => true])
     * @param  bool  $mock  Return a Mockery mock instead of a real model
     */
    public function getMedium(
        ?string $fileName = 'test.jpg',
        string $collection = 'image_collection',
        array $customProperties = [],
        array $generatedConversions = ['thumb' => true],
        bool $mock = false
    ): Media {
        // if mock requested, return a simple mock (useful for view/unit tests)
        if ($mock) {
            $media = \Mockery::mock(Media::class)->shouldIgnoreMissing();

            $media->generated_conversions = $generatedConversions;

            $media->shouldReceive('getCustomProperty')
                ->with('generated_conversions', [])
                ->andReturn($generatedConversions);

            // define common stubs
            foreach (array_keys($generatedConversions) as $conversion) {
                $media->shouldReceive('hasGeneratedConversion')
                    ->with($conversion)
                    ->andReturn($generatedConversions[$conversion]);

                $media->shouldReceive('getUrl')
                    ->with($conversion)
                    ->andReturn("/media/{$conversion}-{$fileName}");

                $media->shouldReceive('getSrcset')
                    ->with($conversion)
                    ->andReturn("/media/{$conversion}-{$fileName} 1x, /media/{$conversion}-2x-{$fileName} 2x");
            }

            // fallback getUrl without conversion
            $media->shouldReceive('getUrl')->withNoArgs()->andReturn("/media/{$fileName}");

            return $media;
        }

        // create a real attached Media model (for integration tests)
        $model = $this->getTestBlogModel();
        $source = __DIR__.'/Support/files/'.$fileName;

        if (! File::exists($source)) {
            throw new \RuntimeException("File {$fileName} does not exist in Support/files.");
        }

        $target = $this->getFixtureUploadedFile($fileName);
        File::ensureDirectoryExists(dirname($target));
        File::copy($source, $target);

        /** @var \Spatie\MediaLibrary\MediaCollections\Models\Media $media */
        $media = $model
            ->addMedia($target)
            ->preservingOriginal()
            ->withCustomProperties($customProperties)
            ->toMediaCollection($collection);

        // manually override generated_conversions if needed
        $media->generated_conversions = $generatedConversions;
        $media->save();

        return $media;
    }

    /**
     * Alias for getMedium() to match naming convention preference.
     */
    public function getMedia(...$args): Media
    {
        return $this->getMedium(...$args);
    }
}
