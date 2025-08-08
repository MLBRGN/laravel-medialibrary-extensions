<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Mlbrgn\MediaLibraryExtensions\Providers\MediaLibraryExtensionsServiceProvider;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Ufo;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Orchestra\Testbench\TestCase as Orchestra;

//class TestCase extends BaseTestCase
class TestCase extends Orchestra
{
    protected $baseUrl = 'http://medialibrary-extensions.test';

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
    }

    protected function getPackageProviders($app): array
    {
        return [
            MediaLibraryExtensionsServiceProvider::class,
//            \Illuminate\Translation\TranslationServiceProvider::class,
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

    protected function createDirectory($directory): void
    {
        if (File::isDirectory($directory)) {
            File::deleteDirectory($directory);
        }
        File::makeDirectory($directory);
    }

    public function getTempDirectory(string $suffix = ''): string
    {
        return __DIR__.'/Support/tmp'.($suffix == '' ? '' : '/'.$suffix);
    }

    public function getMediaDirectory(string $suffix = ''): string
    {
        return $this->getTempDirectory('media'.($suffix == '' ? '' : '/'.$suffix));
    }

    public function getUploadedFile($fileName): string
    {
        return $this->getTempDirectory('uploads/'.$fileName);
    }

    protected function refreshTestFiles(): void
    {
        File::copyDirectory(__DIR__.'/Support/files', $this->getTemporaryUploadsDirectory());
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

    private function getTemporaryUploadsDirectory(): string
    {
        return __DIR__.'/Support/tmp/uploads';
    }

}
