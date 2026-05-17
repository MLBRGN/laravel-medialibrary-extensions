<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Controllers;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;

class DemoController extends Controller
{

    protected function getDemoModel(): Alien
    {
        $model = Alien::with('media')->first() ?? Alien::create();

        if ($model->getMedia('alien-media-lab')->isEmpty()) {
            $demoImage = __DIR__.'/../../../resources/demo/demo.jpg';

            $model
                ->addMedia($demoImage)
                ->preservingOriginal()
                ->toMediaCollection('alien-media-lab', 'media_demo');

            $model->load('media');
        }

        return $model;
    }

    public function demoPlain(): View
    {

        app()->instance('mle-demo-mode', true);

        config(['media-library-extensions.frontend_theme' => 'plain']);

        $model = $this->getDemoModel();

        // Get the first existing model or create it if none exists
//        $model = Alien::with('media')->first() ?? Alien::create();
//
//        // add medium if none exists yet
//        if ($model->getMedia('alien-media-lab')->isEmpty()) {
//            $demoImage = __DIR__.'/../../../resources/demo/demo.jpg';
//
//            $model
//                ->addMedia($demoImage)
//                ->preservingOriginal()
//                ->toMediaCollection('alien-media-lab', 'media_demo');
//
//            // Re-load the media so it's immediately available
//            $model->load('media');
//        }

        $medium = $model->getMedia('alien-media-lab')->first();

        return view('media-library-extensions::demo.mle-plain', [
            'model' => $model,
            'medium' => $medium,
        ]);

    }

    public function demoBootstrap5(): View
    {
        app()->instance('mle-demo-mode', true);

        config(['media-library-extensions.frontend_theme' => 'bootstrap-5']);

        $model = $this->getDemoModel();
        // Get the first existing model or create it if none exists
//        $model = Alien::with('media')->first() ?? Alien::create();
//
//        // add medium if none exists yet
//        if ($model->getMedia('alien-media-lab')->isEmpty()) {
//            $demoImage = __DIR__.'/../../../resources/demo/demo.jpg';
//
//            $model
//                ->addMedia($demoImage)
//                ->preservingOriginal()
//                ->toMediaCollection('alien-media-lab', 'media_demo');
//
//            // Re-load the media so it's immediately available
//            $model->load('media');
//        }

        $medium = $model->getMedia('alien-media-lab')->first();

        return view('media-library-extensions::demo.mle-bootstrap-5', [
            'model' => $model,
            'medium' => $medium,
        ]);
    }
}
