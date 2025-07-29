<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Controllers;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\Aliens;

class DemoController extends Controller
{
    public function demoPlain(): View
    {

        if (!config('media-library-extensions.demo_mode')) {
            abort(403, __('media-library-extensions::messages.demo_mode_disabled'));
        }

        config(['media-library-extensions.frontend_theme' => 'plain']);

        // Get the first existing model or create it if none exists
        $model = Aliens::first() ?? Aliens::create();

        return view('media-library-extensions::components.demo.mle-plain', [
            'model' => $model,
        ]);

    }

    public function demoBootstrap5(): View
    {

        if (!config('media-library-extensions.demo_mode')) {
            abort(403, __('media-library-extensions::messages.demo_mode_disabled'));
        }

        config(['media-library-extensions.frontend_theme' => 'bootstrap-5']);

        // Get the first existing model or create it if none exists
        $model = Aliens::first() ?? Aliens::create();

        return view('media-library-extensions::components.demo.mle-bootstrap-5', [
            'model' => $model,
        ]);
    }

}
