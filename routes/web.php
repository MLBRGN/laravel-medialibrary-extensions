<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Routes;

// Media manager routes
use Illuminate\Support\Facades\Route;
use Mlbrgn\SpatieMediaLibraryExtensions\Http\Controllers\MediaManagerController;
use Mlbrgn\SpatieMediaLibraryExtensions\Http\Controllers\PackageAssetController;

// TODO only auth?
Route::group([
    'prefix' => config('media-library-extensions.prefix'),
    'as' => 'admin.',
    'middleware' => ['preventBackHistory'],
], function () {
    Route::controller(MediaManagerController::class)->group(function () {
        Route::post('media-manager-upload-single', 'mediaUploadSingle')
            ->name('media-upload-single');
        Route::post('media-manager-upload-multiple', 'mediaUploadMultiple')
            ->name('media-upload-multiple');
        Route::post('media-manager-set-medium-as-first-in-collection', 'setMediumAsFirstInCollection')
            ->name('set-medium-as-first-in-collection');
        Route::delete('media-manager/{media}/destroy', 'mediaDestroy')
            ->name('media-destroy');
    });
});

Route::group([
    'prefix' => config('media-library-extensions.prefix'),
], function () {
    Route::get(config('media-library-extensions.prefix').'package/assets/{name}', PackageAssetController::class)
        ->where('name', '[a-zA-Z0-9-]+')
        ->name('package.assets');
});
