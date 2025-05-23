<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Mlbrgn\SpatieMediaLibraryExtensions\Providers\SpatieMediaLibraryExtensionsServiceProvider;
//use Orchestra\Testbench\TestCase as BaseTestCase;
use Mlbrgn\SpatieMediaLibraryExtensions\Tests\Database\Migrations\CreateBlogsTable;
use Mlbrgn\SpatieMediaLibraryExtensions\Tests\Database\Migrations\CreateMediaTable;
use Orchestra\Testbench\BrowserKit\TestCase as BaseTestCase;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TestCase extends BaseTestCase
{

    protected $baseUrl = 'http://activerendwerk.test';

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('app.key', 'base64:BOiGLFUC+84Du2o8GYos0kGJaj4zGX9M9BkLsAj04Ik=');

        $this->app['config']->set('session.serialization', 'php');

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Mlbrgn\\SpatieMediaLibraryExtensions\\Tests\\database\\factories\\' . class_basename($modelName) . 'Factory';
        });

        View::addLocation(__DIR__.'/Feature/views');
    }

    protected function getPackageProviders($app): array
    {
        return [
            SpatieMediaLibraryExtensionsServiceProvider::class,
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

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('media-library.media_model', Media::class);
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        include_once __DIR__ . '/database/migrations/create_media_table.php';
        include_once __DIR__ . '/database/migrations/create_blogs_table.php';
        (new CreateMediaTable())->up();
        (new CreateBlogsTable())->up();
    }

}
