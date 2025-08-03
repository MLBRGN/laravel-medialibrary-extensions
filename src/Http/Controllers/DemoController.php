<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Controllers;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;

class DemoController extends Controller
{
    public function demoPlain(): View
    {

        config(['media-library-extensions.frontend_theme' => 'plain']);

        // Get the first existing model or create it if none exists
        $model = Alien::first() ?? Alien::create();

        return view('media-library-extensions::components.demo.mle-plain', [
            'model' => $model,
        ]);

    }

    public function demoBootstrap5(): View
    {
        config(['media-library-extensions.frontend_theme' => 'bootstrap-5']);

        // Get the first existing model or create it if none exists
        $model = Alien::first() ?? Alien::create();

        return view('media-library-extensions::components.demo.mle-bootstrap-5', [
            'model' => $model,
        ]);
    }
}
