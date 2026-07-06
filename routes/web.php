<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Routes;

use Illuminate\Support\Facades\Route;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\DemoController;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\MediaManagerController;


Route::group([
    'middleware' => array_merge(
        config('medialibrary-extensions.route_middleware', ['web', 'auth']),
        []
    ),
//    'middleware' => config('medialibrary-extensions.route_middleware', ['web']),
    'prefix' => config('medialibrary-extensions.route_prefix'),
], function () {

    Route::controller(MediaManagerController::class)->group(function () {
        Route::post('media-manager-upload-single', 'store')->name(config('medialibrary-extensions.route_prefix').'-media-upload-single');
        Route::post('media-manager-upload-multiple', 'storeMany')->name(config('medialibrary-extensions.route_prefix').'-media-upload-multiple');
        Route::post('media-manager-upload-youtube', 'storeYouTube')->name(config('medialibrary-extensions.route_prefix').'-media-upload-youtube');
        Route::delete('media-manager/{mediaId}/destroy', 'destroy')->name(config('medialibrary-extensions.route_prefix').'-destroy-media');
        Route::delete('media-manager/{temporaryUploadId}/destroy-temporary-upload', 'destroyTemporaryUpload')->name(config('medialibrary-extensions.route_prefix').'-destroy-temporary-upload');
        Route::put('media-manager-set-medium-as-first-in-collection', 'setAsFirst')->name(config('medialibrary-extensions.route_prefix').'-set-as-first');
        Route::put('media-manager-set-temporary-upload-as-first-in-collection', 'setAsFirstTemporaryUpload')->name(config('medialibrary-extensions.route_prefix').'-temporary-upload-set-as-first');
        Route::get('media-manager-preview-update', 'getUpdatedMediaManagerPreviewerHTML')->name(config('medialibrary-extensions.route_prefix').'-media-manager-preview-update');
        Route::get('media-lab-preview-base-update', 'getUpdatedMediaLabPreviewerBaseHTML')->name(config('medialibrary-extensions.route_prefix').'-media-lab-preview-base-update');
        Route::get('media-lab-preview-original-update', 'getUpdatedMediaLabPreviewerOriginalHTML')->name(config('medialibrary-extensions.route_prefix').'-media-lab-preview-original-update');
        Route::post('media-manager/{mediaId}/save-updated-media', 'storeUpdatedMedia')->name(config('medialibrary-extensions.route_prefix').'-save-updated-media');
        Route::post('media-manager/{temporaryUploadId}/save-updated-temporary-upload', 'storeUpdatedTemporaryUpload')->name(config('medialibrary-extensions.route_prefix').'-save-updated-temporary-upload');
        Route::post('media-lab/{mediaId}/restore-original-medium', 'restoreOriginalMedium')->name(config('medialibrary-extensions.route_prefix').'-restore-original-medium');

        // TinyMCE media manager route
        Route::get('media-manager-tinymce', 'tinyMce')
            ->name(config('medialibrary-extensions.route_prefix').'-media-manager-tinymce');

    });
});

Route::group([
    'middleware' => array_merge(
        config('medialibrary-extensions.route_middleware', ['web']),
        []
    ),
//    'middleware' => config('medialibrary-extensions.route_middleware', ['web']),
    'prefix' => config('medialibrary-extensions.route_prefix'),
], function () {
    Route::get('mle-demo', DemoController::class)->name('mle-demo');
});

Route::get('/favicon.ico', fn () => response()->noContent());
