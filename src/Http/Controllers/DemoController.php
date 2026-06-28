<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

class DemoController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless(
            config('medialibrary-extensions.demo_pages_enabled'),
            404
        );

        config(['medialibrary-extensions.disks.media_originals' =>
            [
                'driver' => 'local',
                'root' => storage_path('app/public/media_originals'),
                'url' => config('app.url').'/storage/media_originals', // URL to access files
                'visibility' => 'public',
            ]
        ]);

        $frontendTheme = $request->query('theme', config('medialibrary-extensions.frontend_theme', 'bootstrap-5'));
        $useXhr = $request->boolean('use_xhr', config('medialibrary-extensions.use_xhr', true));
        $dataSource = $request->query('data_source', 'demo');

//        if ($dataSource === 'default') {
//            $dataSource = 'default';
//        }

        // Apply to config so components pick it up as default if not overridden in options
        config([
            'medialibrary-extensions.frontend_theme' => $frontendTheme,
            'medialibrary-extensions.use_xhr' => $useXhr,
        ]);

        $model = $this->getDemoModel($dataSource);

        // Prefer a specifically prepared Lab medium; otherwise reuse existing uploads
        $media = $model->getMedia('alien-media-lab')->first();

//        if ($media === null) {
//            $media = $model->getMedia('alien-single-image')->first()
//                ?: $model->getMedia('alien-multiple-images')->first()
//                ?: optional($model->media)->first();
//        }

//        if ($media === null) {
//            $media = $model->getMedia('alien-single-image')->first()
//                ?: $model->getMedia('alien-multiple-images')->first()
//                ?: optional($model->media)->first();
//        }
//
//        // As a last resort for the demo page: create one demo image so the Lab can render
        if ($media === null && (bool) config('medialibrary-extensions.demo_pages_enabled')) {
            $demoImage = __DIR__.'/../../../resources/demo/demo_small.jpeg';

            if (file_exists($demoImage)) {
                $model
                    ->addMedia($demoImage)
                    ->preservingOriginal()
                    ->toMediaCollection('alien-media-lab', config('medialibrary-extensions.media_disks.demo'));

                $model->load('media');
                $media = $model->getMedia('alien-media-lab')->first();
            }
        }

        return view('medialibrary-extensions::demo.mle-unified', [
            'model' => $model,
            'media' => $media,
            'dataSource' => $dataSource,
            'frontendTheme' => $frontendTheme,
            'useXhr' => $useXhr,
        ]);
    }

//    public function __invoke(Request $request): View
//    {
//        abort_unless(
//            config('medialibrary-extensions.demo_pages_enabled'),
//            404
//        );
//
//        config(['medialibrary-extensions.disks.media_originals' =>
//            [
//                'driver' => 'local',
//                'root' => storage_path('app/public/media_originals'),
//                'url' => config('app.url').'/storage/media_originals', // URL to access files
//                'visibility' => 'public',
//            ]
//        ]);
//
//        $frontendTheme = $request->query('theme', config('medialibrary-extensions.frontend_theme', 'bootstrap-5'));
//        $useXhr = $request->boolean('use_xhr', config('medialibrary-extensions.use_xhr', true));
//        $dataSource = $request->query('data_source', 'demo');
//
////        if ($dataSource === 'default') {
////            $dataSource = 'default';
////        }
//
//        // Apply to config so components pick it up as default if not overridden in options
//        config([
//            'medialibrary-extensions.frontend_theme' => $frontendTheme,
//            'medialibrary-extensions.use_xhr' => $useXhr,
//        ]);
//
//        $model = $this->getDemoModel($dataSource);
//
//        $media = $model->getMedia('alien-media-lab')->first();
//
//        return view('medialibrary-extensions::demo.mle-unified', [
//            'model' => $model,
//            'media' => $media,
//            'dataSource' => $dataSource,
//            'frontendTheme' => $frontendTheme,
//            'useXhr' => $useXhr,
//        ]);
//    }

    protected function getDemoModel(?string $dataSource = 'default'): Alien
    {
        $model = new Alien;

        if ($dataSource) {
            $connection = app(DataSourceResolver::class)->resolveConnection($dataSource);
            $model->setConnection($connection);
        }

        $existingModel = $model->newQuery()->with('media')->first();

        if (! $existingModel) {
            $existingModel = $model->newQuery()->create();
        }

        // There could be a bug here, no datasources support
//        if ($existingModel->getMedia('alien-media-lab')->isEmpty()) {
//
//            $demoImage = __DIR__.'/../../../resources/demo/demo_small.jpeg';
//
//            if (file_exists($demoImage)) {
//                $existingModel
//                    ->addMedia($demoImage)
//                    ->preservingOriginal()
//                    ->toMediaCollection('alien-media-lab', config('medialibrary-extensions.media_disks.demo'));
//
//                $existingModel->load('media');
//            }
//        }


        return $existingModel;
    }
}
