<?php

namespace Mlbrgn\MediaLibraryExtensions\Routes;

use Illuminate\Support\Facades\Route;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\MediaManagerController;

// TODO authentication
Route::group([
    'middleware' => config('media-library-extensions.route_middleware'),
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
        });
    Route::get('mle-plain', function () {
        return view('media-library-extensions::components.test.mle-plain-test');
    })->name('mle-plain-test');
    Route::get('mle-bootstrap-5', function () {
        return view('media-library-extensions::components.test.mle-bootstrap-5-test');
    })->name('mle-bootstrap-test');
});
