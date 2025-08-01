<?php

namespace Mlbrgn\MediaLibraryExtensions\Routes;

use Illuminate\Support\Facades\Route;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\DemoController;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\MediaManagerController;
use Mlbrgn\MediaLibraryExtensions\Http\Middleware\UseDemoModeConnection;

Route::group([
    'middleware' => config('media-library-extensions.route_middleware', []),
    'prefix' => config('media-library-extensions.route_prefix'),
], function () {
    Route::controller(MediaManagerController::class)->group(function () {
        Route::post('media-manager-upload-single', 'store')->name(config('media-library-extensions.route_prefix').'-media-upload-single');
        Route::post('media-manager-upload-multiple', 'storeMany')->name(config('media-library-extensions.route_prefix').'-media-upload-multiple');
        Route::post('media-manager-upload-youtube', 'storeYouTube')->name(config('media-library-extensions.route_prefix').'-media-upload-youtube');
        Route::delete('media-manager/{media}/destroy', 'destroy')->name(config('media-library-extensions.route_prefix').'-medium-destroy');
        Route::delete('media-manager/{temporaryUpload}/temporary-upload-destroy', 'temporaryUploadDestroy')->name(config('media-library-extensions.route_prefix').'-temporary-upload-destroy');
        Route::put('media-manager-set-medium-as-first-in-collection', 'setAsFirst')->name(config('media-library-extensions.route_prefix').'-set-as-first');
        Route::put('media-manager-set-temporary-upload-as-first-in-collection', 'setTemporaryUploadAsFirst')->name(config('media-library-extensions.route_prefix').'-temporary-upload-set-as-first');
        Route::get('/media-manager-preview-update', 'getUpdatedPreviewerHTML')->name(config('media-library-extensions.route_prefix').'-preview-update');
        Route::post('media-manager/{media}/save-updated-medium', 'saveUpdatedMedium')->name(config('media-library-extensions.route_prefix').'-save-updated-medium');
    });
});

if (config('media-library-extensions.demo_pages_enabled')) {
    Route::group([
        'middleware' => array_merge(
            config('media-library-extensions.route_middleware'),
            [UseDemoModeConnection::class]
        ),
        'prefix' => config('media-library-extensions.route_prefix'),
    ], function () {
        Route::controller(DemoController::class)->group(function () {
            Route::get('mle-demo-plain', 'demoPlain')->name('mle-demo-plain');
            Route::get('mle-demo-bootstrap-5', 'demoBootstrap5')->name('mle-demo-bootstrap-5');
        });
    });
}
