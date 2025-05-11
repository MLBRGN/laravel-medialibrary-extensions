<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Routes;

// Media manager routes
use Illuminate\Support\Facades\Route;
use Mlbrgn\SpatieMediaLibraryExtensions\Http\Controllers\MediaManagerController;

// TODO only auth?
Route::group([
    'prefix' => 'admin',
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
