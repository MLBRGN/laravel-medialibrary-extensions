<?php

/** @noinspection SpellCheckingInspection */

namespace Mlbrgn\SpatieMediaLibraryExtensions\Tests;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Mlbrgn\LaravelFormComponents\Providers\FormComponentsServiceProvider;
use Orchestra\Testbench\BrowserKit\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $baseUrl = 'http://activerendwerk.test'; // TODO not activerendwerk.test

    public static function isLaravel10(): bool
    {
        return version_compare(app()->version(), '10.0', '>=');
    }

    //    protected function setUp(): void
    //    {
    //        parent::setUp();
    //
    //        $this->app['config']->set('app.key', 'base64:BOiGLFUC+84Du2o8GYos0kGJaj4zGX9M9BkLsAj04Ik=');
    //
    //        $this->app['config']->set('session.serialization', 'php');
    //
    //        $this->app['config']->set('form-components.framework', env('FORM_COMPONENTS_FRAMEWORK', 'bootstrap-5'));
    //
    //        View::addLocation(__DIR__.'/Feature/views');
    //    }
    //
    //    protected function getPackageProviders($app): array
    //    {
    //        return [FormComponentsServiceProvider::class];
    //    }
    //
    //    public function registerTestRoute($uri, ?callable $post = null): self
    //    {
    //        Route::middleware('web')->group(function () use ($uri, $post) {
    //            Route::view($uri, $uri);
    //
    //            if ($post) {
    //                Route::post($uri, $post);
    //            }
    //        });
    //
    //        return $this;
    //    }
}
