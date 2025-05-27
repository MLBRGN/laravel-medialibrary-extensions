<?php

namespace Mlbrgn\MediaLibraryExtensions\Routes;

use Illuminate\Support\Facades\Route;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\MediaManagerController;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\PackageAssetController;

// TODO authentication
Route::group([
    'middleware' => ['web'], // !important needed for session to work
    'prefix' => config('media-library-extensions.route_prefix'),
], function () {
    Route::controller(MediaManagerController::class)
        ->group(function () {
            Route::post('media-manager-upload-single', 'store')
                ->name(config('media-library-extensions.route_prefix').'-media-upload-single');
            Route::post('media-manager-upload-multiple', 'storeMany')
                ->name(config('media-library-extensions.route_prefix').'-media-upload-multiple');
            Route::delete('media-manager/{media}/destroy', 'destroy')
                ->name(config('media-library-extensions.route_prefix').'-medium-destroy');
            //            Route::delete('media-manager/{medium}/destroy', 'destroy')
            //                ->name(config('media-library-extensions.route_prefix').'-medium-destroy');
            Route::post('media-manager-set-medium-as-first-in-collection', 'setAsFirst')
                ->name(config('media-library-extensions.route_prefix').'-set-as-first');
        });
});

// Route::group([
//    'prefix' => config('media-library-extensions.route_prefix'),
// ], function () {
//    Route::get(config('media-library-extensions.prefix').'package/assets/{name}', PackageAssetController::class)
//        ->where('name', '[a-zA-Z0-9-\.{1}]+')
//        ->name(config('media-library-extensions.route_prefix').'-package.assets');
// });
