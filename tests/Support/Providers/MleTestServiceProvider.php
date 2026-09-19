<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Support\Providers;

use Illuminate\Support\ServiceProvider;
use Mlbrgn\MediaLibraryExtensions\Tests\Support\Middleware\MleTestConfigMiddleware;

class MleTestServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('mle_test_config', MleTestConfigMiddleware::class);
        $router->pushMiddlewareToGroup('web', 'mle_test_config');

        $this->app['config']->set('medialibrary-extensions.route_middleware', [
            'web',
            'mle_test_config',
        ]);
    }
}
