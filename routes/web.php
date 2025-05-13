<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Routes;

// Media manager routes
use Illuminate\Support\Facades\Route;
use Mlbrgn\SpatieMediaLibraryExtensions\Http\Controllers\MediaManagerController;
use Mlbrgn\SpatieMediaLibraryExtensions\Http\Controllers\PackageAssetController;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// TODO only auth?
Route::group([
    //    'middleware' => ['web'],
    'prefix' => config('media-library-extensions.route-prefix'),
], function () {
    Route::controller(MediaManagerController::class)
        ->group(function () {
            Route::post('media-manager-upload-single', 'mediaUploadSingle')
                ->name(config('media-library-extensions.route-prefix').'-media-upload-single');
            Route::post('media-manager-upload-multiple', 'mediaUploadMultiple')
                ->name(config('media-library-extensions.route-prefix').'-media-upload-multiple');
            Route::post('media-manager-set-medium-as-first-in-collection', 'setMediumAsFirstInCollection')
                ->name(config('media-library-extensions.route-prefix').'-set-as-first');
            //            Route::delete('media-manager/{media}/destroy', 'mediaDestroy')
            //                ->name(config('media-library-extensions.route-prefix').'-medium-destroy');
            Route::delete('media-manager/{medium}/destroy', 'mediumDestroy')
                ->name(config('media-library-extensions.route-prefix').'-medium-destroy');
        });
});

Route::group([
    'prefix' => config('media-library-extensions.route-prefix'),
], function () {
    Route::get(config('media-library-extensions.prefix').'package/assets/{name}', PackageAssetController::class)
        ->where('name', '[a-zA-Z0-9-\.{1}]+')
        ->name(config('media-library-extensions.route-prefix').'-package.assets');
});
