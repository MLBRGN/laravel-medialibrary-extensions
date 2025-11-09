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
        $model = Alien::with('media')->first() ?? Alien::create();

        // add medium if none exists yet
        if ($model->getMedia('alien-media-lab')->isEmpty()) {
            $demoImage = __DIR__ . '/../../../resources/images/demo.jpg';

            $model
                ->addMedia($demoImage)
                ->preservingOriginal()
                ->toMediaCollection('alien-media-lab', 'media_demo');
//                ->toMediaCollection('alien-media-lab', 'media_demo');

            // Re-load the media so it's immediately available
            $model->load('media');
        }

        $medium = $model->getMedia('alien-media-lab')->first();

        return view('media-library-extensions::demo.mle-plain', [
            'model' => $model,
            'medium' => $medium,
        ]);

    }

    public function demoBootstrap5(): View
    {
        config(['media-library-extensions.frontend_theme' => 'bootstrap-5']);

        // Get the first existing model or create it if none exists
        $model = Alien::with('media')->first() ?? Alien::create();

        // add medium if none exists yet
        if ($model->getMedia('alien-media-lab')->isEmpty()) {
            $demoImage = __DIR__ . '/../../../resources/images/demo.jpg';

            $model
                ->addMedia($demoImage)
                ->preservingOriginal()
                ->toMediaCollection('alien-media-lab', 'media_demo');

            // Re-load the media so it's immediately available
            $model->load('media');
        }

        $medium = $model->getMedia('alien-media-lab')->first();

        return view('media-library-extensions::demo.mle-bootstrap-5', [
            'model' => $model,
            'medium' => $medium,
        ]);
    }
}
