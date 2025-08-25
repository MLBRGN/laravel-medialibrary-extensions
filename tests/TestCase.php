<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
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
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Orchestra\Testbench\TestCase as Orchestra;

//class TestCase extends BaseTestCase
class TestCase extends Orchestra
{
    protected $baseUrl = 'http://medialibrary-extensions.test';
    protected Blog $testModel;
    protected Ufo $testModelNotExtendingHasMedia;

    // runs before every test
    protected function setUp(): void
    {
        parent::setUp();

        $this->testModel = Blog::create(['title' => 'Test Model']);
        $this->testModelNotExtendingHasMedia = Ufo::create(['title' => 'Test Model']);
        $this->app['translator']->addNamespace(
            'medialibrary-extensions',
            __DIR__ . '/../lang'
        );

        Route::get('/login', fn () => 'Login (dummy)')->name('login');

        Config::set('media-library-extensions.demo_pages_enabled', false);

        if (empty(config('app.key'))) {
            $key = 'base64:'.base64_encode(random_bytes(32));
            Config::set('app.key', $key);
        }

        // Use a persistent session driver
//        Config::set('session.driver', 'file');
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

        // Load media library config (needed for tests that interact with media library to work)
        $app['config']->set('media-library', require __DIR__ . '/config/media-library.php');

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

    protected function defineDatabaseMigrations(): void {
        // also loads migrations from service provider!!!
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
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

    public function getFixtureUploadedFile($fileName): string
    {
        return $this->getTempDirectory('uploads/'.$fileName);
    }

    protected function getUploadedFile(
        string $name = 'test.jpg',
        int $sizeInKb = 10,
        string $mimeType = 'image/jpeg'
    ): UploadedFile {
        // Ensure the local test disk exists
        Storage::fake('local');

        // Create a temporary file
        $path = sys_get_temp_dir() . '/' . $name;
        file_put_contents($path, str_repeat('a', $sizeInKb * 1024));

        return new UploadedFile(
            $path,
            $name,
            $mimeType,
            null,
            true // mark it as test file
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

    public function getUser(): User {
        return new User(['id' => 123, 'name' => 'Test User']);
    }


    public function getTestBlogModel(): Model {
        return $this->testModel;
    }

    public function getTestModelNotExtendingHasMedia() {
        return $this->testModelNotExtendingHasMedia;
    }

    public function getMediaModel($id = 1): Model {
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
            'path' => 'uploads/' . $fileName,
            'name' => pathinfo($fileName, PATHINFO_FILENAME),
            'size' => 1024024,
            'file_name' => $fileName,
            'collection_name' => 'test',
            'custom_properties' => ['image_collection' => 'images'],
            'session_id' => session()->getId(),
        ];

        return TemporaryUpload::create(array_merge($defaults, $overrides));
    }

//    public function getModelWithMedia(
//        string $collection = 'images',
//        int|array $files = 1 // number of files or explicit file names
//    ) {
//        $model = $this->getTestBlogModel();
//
//        $available = [
//            'test.jpg',
//            'test2.jpg',
//            'test3.jpg',
//            'test.png',
//            'test2.png',
//            'test3.png',
//        ];
//
//        // Normalize input
//        if (is_int($files)) {
//            $count = $files;  // keep the integer separately
//            $files = [];
//            for ($i = 0; $i < $count; $i++) {
//                $files[] = $available[$i % count($available)];
//            }
//        }
//
//        foreach ($files as $fileName) {
//            $source = __DIR__ . '/Support/files/' . $fileName;
//
//            if (! File::exists($source)) {
//                throw new \RuntimeException("Test file '{$fileName}' does not exist in Support/files.");
//            }
//
//            $target = $this->getFixtureUploadedFile($fileName);
//            File::ensureDirectoryExists(dirname($target));
//
//            if (! File::exists($target)) {
//                File::copy($source, $target);
//            }
//
//            $model
//                ->addMedia($target)
//                ->preservingOriginal()
//                ->toMediaCollection($collection);
//        }
//
//        return $model->fresh();
//    }

    public function getTestImagePath(string $fileName = 'test.jpg'): string
    {
        $source = __DIR__ . '/Support/files/' . $fileName;
        $target = $this->getFixtureUploadedFile($fileName);

        File::ensureDirectoryExists(dirname($target));
        File::copy($source, $target);

        return $target;
    }

//    public function getModelWithMedia(
//        string $collection = 'images',
//        int|array $files = 1 // number of files or explicit file names
//    ) {
//        $model = $this->getTestBlogModel();
//
//        $available = [
//            'test.jpg',
//            'test2.jpg',
//            'test3.jpg',
//            'test.png',
//            'test2.png',
//            'test3.png',
//            'test.mp4',
//            'test.mp3',
//        ];
//
//        // Normalize input
//        if (is_int($files)) {
//            $count = $files;
//            $files = [];
//            for ($i = 0; $i < $count; $i++) {
//                $files[] = $available[$i % count($available)];
//            }
//        }
//
//        foreach ($files as $fileName) {
//            $source = __DIR__ . '/Support/files/' . $fileName;
//
//            if (! File::exists($source)) {
//                throw new \RuntimeException("Test file '{$fileName}' does not exist in Support/files.");
//            }
//
//            $target = $this->getFixtureUploadedFile($fileName);
//            File::ensureDirectoryExists(dirname($target));
//
//            if (! File::exists($target)) {
//                File::copy($source, $target);
//            }
//
//            $model
//                ->addMedia($target)
//                ->preservingOriginal()
//                ->toMediaCollection($collection);
//        }
//
//        return $model->fresh();
//    }
//
//    /**
//     * Generic helper for any test file (jpg, png, mp4, mp3, …).
//     */
//    public function getTestFilePath(string $fileName = 'test.jpg'): string
//    {
//        $source = __DIR__ . '/Support/files/' . $fileName;
//
//        if (! File::exists($source)) {
//            throw new \RuntimeException("Test file '{$fileName}' does not exist in Support/files.");
//        }
//
//        $target = $this->getFixtureUploadedFile($fileName);
//
//        File::ensureDirectoryExists(dirname($target));
//        File::copy($source, $target);
//
//        return $target;
//    }

    public function getModelWithMedia(array $types = ['image' => 1]): Blog
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

        foreach ($types as $type => $count) {
            if (! isset($pool[$type])) {
                throw new \InvalidArgumentException("Unsupported media type '{$type}'");
            }

            $files = [];
            for ($i = 0; $i < $count; $i++) {
                $files[] = $pool[$type][$i % count($pool[$type])];
            }

            foreach ($files as $fileName) {
                $source = __DIR__ . '/Support/files/' . $fileName;

                if (! File::exists($source)) {
                    throw new \RuntimeException("Test file '{$fileName}' does not exist in Support/files.");
                }

                $target = $this->getFixtureUploadedFile($fileName);
                File::ensureDirectoryExists(dirname($target));

                if (! File::exists($target)) {
                    File::copy($source, $target);
                }

                $model
                    ->addMedia($target)
                    ->preservingOriginal()
                    ->toMediaCollection($collectionMap[$type]);
            }
        }

        return $model->fresh();
    }

    public function getTestFilePath(string $fileName): string
    {
        $source = __DIR__ . '/Support/files/' . $fileName;

        if (! File::exists($source)) {
            throw new \RuntimeException("Test file '{$fileName}' does not exist in Support/files.");
        }

        $target = $this->getFixtureUploadedFile($fileName);
        File::ensureDirectoryExists(dirname($target));
        File::copy($source, $target);

        return $target;
    }

}
