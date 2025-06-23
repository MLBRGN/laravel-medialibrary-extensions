<?php

namespace Mlbrgn\MediaLibraryExtensions\Routes;

use Illuminate\Support\Facades\Route;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\DemoController;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\MediaManagerController;
use Mlbrgn\MediaLibraryExtensions\Http\Middleware\UseDemoModeConnection;

$demoMiddleware = config('media-library-extensions.demo_mode') ? [UseDemoModeConnection::class] : [];

Route::group([
    'middleware' => array_merge(
        config('media-library-extensions.route_middleware'),
        $demoMiddleware
    ),
    'prefix' => config('media-library-extensions.route_prefix'),
], function () {
    Route::controller(MediaManagerController::class)
        ->group(function () {
            Route::post('media-manager-upload-single', 'store')
                ->name(config('media-library-extensions.route_prefix').'-media-upload-single');
            Route::post('media-manager-upload-multiple', 'storeMany')
                ->name(config('media-library-extensions.route_prefix').'-media-upload-multiple');
            Route::post('media-manager-upload-youtube', 'storeYouTube')
                ->name(config('media-library-extensions.route_prefix').'-media-upload-youtube');
            Route::delete('media-manager/{media}/destroy', 'destroy')
                ->name(config('media-library-extensions.route_prefix').'-medium-destroy');
            Route::post('media-manager-set-medium-as-first-in-collection', 'setAsFirst')
                ->name(config('media-library-extensions.route_prefix').'-set-as-first');
            Route::get('/media-manager-refresh-preview', 'getMediaPreviewerHTML')
                ->name(config('media-library-extensions.route_prefix').'-media-upload-refresh-preview');
//            Route::get('media-manager-edit-image', 'editImage')
//                ->name(config('media-library-extensions.route_prefix').'-edit-image');
            // using post because technically i delete the old medium and add a new one and no method spoofing needed
            Route::post('media-manager/{media}/save-updated-medium', 'saveUpdatedMedium')
                ->name(config('media-library-extensions.route_prefix').'-save-updated-medium');
        });

    Route::controller(DemoController::class)
        ->group(function () {
            Route::get('mle-demo-plain', 'demoPlain')->name('mle-demo-plain');
            Route::get('mle-demo-bootstrap-5', 'demoBootstrap5')->name('mle-demo-bootstrap-5');
        });
});

