<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
use Mlbrgn\MediaLibraryExtensions\Models\demo\DemoMedia;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

class DemoController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless(
            config('medialibrary-extensions.demo_pages_enabled'),
            404
        );

        config(['medialibrary-extensions.disks.media_originals' => [
            'driver' => 'local',
            'root' => storage_path('app/public/media_originals'),
            'url' => config('app.url').'/storage/media_originals', // URL to access files
            'visibility' => 'public',
        ],
        ]);

        $theme = $request->query('theme', config('medialibrary-extensions.frontend_theme', 'bootstrap-5'));
        $useXhr = $request->boolean('use_xhr', config('medialibrary-extensions.use_xhr', true));
        $dataSource = $request->query('data_source', 'demo');

        //        if ($dataSource === 'default') {
        //            $dataSource = 'default';
        //        }

        // Apply to config so components pick it up as default if not overridden in options
        config([
            'medialibrary-extensions.frontend_theme' => $theme,
            'medialibrary-extensions.use_xhr' => $useXhr,
            //            'media-library.media_model' => DemoMedia::class,
        ]);

        //        // TODO Works? Needed?
        //        config()->set('media-library.media_model', DemoMedia::class);
        //
        //        // set default database (note this does not mean it will use this database, that depends on
        //        // what dataSource is set to, if set to default it uses this connection, otherwise the other configured connection
        //
        // //        dd(base_path('/storage/app/mle/mle-browser-tests-demo-host-app.sqlite'));
        //        $pathToHostAppTestDb = base_path('/storage/app/mle/demo/mle-demo-host-app.sqlite');
        //        $pathToDemoTestDb = base_path('/storage/app/mle/demo/mle-demo.sqlite');
        //
        //        // create the database files if they don't exist
        //        if (!file_exists($pathToHostAppTestDb)) {
        //            touch($pathToHostAppTestDb);
        //        }
        //
        //        if (!file_exists($pathToDemoTestDb)) {
        //            touch($pathToDemoTestDb);
        //        }
        //
        //        // configure the database connections
        //
        // //        config()->set('database.connections.default', [
        // //            'driver' => 'sqlite',
        // //            'database' => $pathToHostAppTestDb,
        // //            'prefix' => '',
        // //        ]);
        //
        //        config()->set('database.connections.mle_demo_host_app', [
        //            'driver' => 'sqlite',
        //            'database' => $pathToHostAppTestDb,
        //            'prefix' => '',
        //        ]);
        //
        //        config()->set('database.connections.mle_demo', [
        //            'driver' => 'sqlite',
        //            'database' => $pathToDemoTestDb,
        //            'prefix' => '',
        //        ]);
        //
        //        // set the database connections to use (DataSourceResolver looks in data_sources.xxxx.connection)
        // //        config()->set('database.default', 'mle_demo_host_app');
        //        config()->set('medialibrary-extensions.data_sources.default.connection', 'mle_demo_host_app');
        //        config()->set('medialibrary-extensions.data_sources.demo.connection', 'mle_demo');

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
            'theme' => $theme,
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
    //        $theme = $request->query('theme', config('medialibrary-extensions.frontend_theme', 'bootstrap-5'));
    //        $useXhr = $request->boolean('use_xhr', config('medialibrary-extensions.use_xhr', true));
    //        $dataSource = $request->query('data_source', 'demo');
    //
    // //        if ($dataSource === 'default') {
    // //            $dataSource = 'default';
    // //        }
    //
    //        // Apply to config so components pick it up as default if not overridden in options
    //        config([
    //            'medialibrary-extensions.frontend_theme' => $theme,
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
    //            'theme' => $theme,
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

        // There could be a bug here, no data sources support
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
