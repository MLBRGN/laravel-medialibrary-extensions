<?php

namespace Mlbrgn\MediaLibraryExtensions\Routes;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\DemoController;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\MediaManagerController;
use Mlbrgn\MediaLibraryExtensions\Http\Middleware\RegisterDemoDatabase;
//use Spatie\MediaLibrary\MediaCollections\Models\Media;

//use Mlbrgn\MediaLibraryExtensions\Models\Media;

Route::group([
    'middleware' => array_merge(
        config('media-library-extensions.route_middleware', ['web', 'auth']),
        [RegisterDemoDatabase::class]
    ),
    'prefix' => config('media-library-extensions.route_prefix'),
], function () {

//    app()->instance('mle-demo-mode', true);

    Route::controller(MediaManagerController::class)->group(function () {
        Route::post('media-manager-upload-single', 'store')->name(config('media-library-extensions.route_prefix').'-media-upload-single');
        Route::post('media-manager-upload-multiple', 'storeMany')->name(config('media-library-extensions.route_prefix').'-media-upload-multiple');
        Route::post('media-manager-upload-youtube', 'storeYouTube')->name(config('media-library-extensions.route_prefix').'-media-upload-youtube');
        Route::delete('media-manager/{mediaId}/destroy', 'destroy')->name(config('media-library-extensions.route_prefix').'-destroy-media');
        Route::delete('media-manager/{temporaryUploadId}/destroy-temporary-upload', 'destroyTemporaryUpload')->name(config('media-library-extensions.route_prefix').'-destroy-temporary-upload');
        Route::put('media-manager-set-medium-as-first-in-collection', 'setAsFirst')->name(config('media-library-extensions.route_prefix').'-set-as-first');
        Route::put('media-manager-set-temporary-upload-as-first-in-collection', 'setAsFirstTemporaryUpload')->name(config('media-library-extensions.route_prefix').'-temporary-upload-set-as-first');
        Route::get('media-manager-preview-update', 'getUpdatedMediaManagerPreviewerHTML')->name(config('media-library-extensions.route_prefix').'-media-manager-preview-update');
        Route::get('media-manager-lab-preview-update', 'getUpdatedMediaManagerLabPreviewerHTML')->name(config('media-library-extensions.route_prefix').'-media-manager-lab-preview-update');
        Route::post('media-manager/{mediaId}/save-updated-media', 'saveUpdatedMedia')->name(config('media-library-extensions.route_prefix').'-save-updated-media');
        Route::post('media-manager/{temporaryUploadId}/save-updated-temporary-upload', 'saveUpdatedTemporaryUpload')->name(config('media-library-extensions.route_prefix').'-save-updated-temporary-upload');
        Route::post('media-lab/{media}/restore-original-medium', 'restoreOriginalMedium')->name(config('media-library-extensions.route_prefix').'-restore-original-medium');

        // TinyMCE media manager route
        Route::get('media-manager-tinymce', 'tinyMce')
            ->name(config('media-library-extensions.route_prefix').'-media-manager-tinymce');

    });
});

if (config('media-library-extensions.demo_pages_enabled')) {
    Route::group([
        'middleware' => array_merge(
            config('media-library-extensions.route_middleware', ['web']),
            [RegisterDemoDatabase::class]
        ),
        'prefix' => config('media-library-extensions.route_prefix'),
    ], function () {
        Route::controller(DemoController::class)->group(function () {
            Route::get('mle-demo-plain', 'demoPlain')->name('mle-demo-plain');
            Route::get('mle-demo-bootstrap-5', 'demoBootstrap5')->name('mle-demo-bootstrap-5');
        });

        Route::get('favicon.ico', function () {
            $path = __DIR__.'/../resources/assets/favicon.ico';

            if (! file_exists($path)) {
                abort(404);
            }

            return Response::file($path, [
                'Content-Type' => 'image/x-icon',
                'Cache-Control' => 'public, max-age=31536000', // cache for 1 year
            ]);
        })->name('mlbrgn.mle.favicon');
    });
}
