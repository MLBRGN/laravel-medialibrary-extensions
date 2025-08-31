<?php

namespace Mlbrgn\MediaLibraryExtensions\Routes;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\DemoController;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\MediaManagerController;
use Mlbrgn\MediaLibraryExtensions\Http\Middleware\UseDemoModeConnection;
use Mlbrgn\MediaLibraryExtensions\Models\Media;

// Prepare base middleware from config
$baseMiddleware = config('media-library-extensions.route_middleware', []);

// Conditionally add RegisterDemoDatabase if demo pages are enabled
// if (config('media-library-extensions.demo_pages_enabled')) {
//    $baseMiddleware[] = RegisterDemoDatabase::class;
// }
Route::group([
    'middleware' => config('media-library-extensions.route_middleware', ['web', 'auth']),
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
        Route::get('media-manager-preview-update', 'getUpdatedPreviewerHTML')->name(config('media-library-extensions.route_prefix').'-preview-update');
        Route::post('media-manager/{media}/save-updated-medium', 'saveUpdatedMedium')->name(config('media-library-extensions.route_prefix').'-save-updated-medium');
        Route::post('media-manager/{temporaryUpload}/save-updated-temporary-upload', 'saveUpdatedTemporaryUpload')->name(config('media-library-extensions.route_prefix').'-save-updated-temporary-upload');

        // TinyMCE media manager route
        Route::get('media-manager-tinymce', 'tinyMce')
            ->name(config('media-library-extensions.route_prefix') . '-media-manager-tinymce');
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
        // Local route model binding
        Route::bind('media', fn ($value) => Media::findOrFail($value));

        Route::controller(DemoController::class)->group(function () {
            Route::get('mle-demo-plain', 'demoPlain')->name('mle-demo-plain');
            Route::get('mle-demo-bootstrap-5', 'demoBootstrap5')->name('mle-demo-bootstrap-5');
        });

        Route::get('favicon.ico', function () {
            $path = __DIR__ . '/../resources/assets/favicon.ico';

            if (! file_exists($path)) {
                abort(404);
            }

            return Response::file($path, [
                'Content-Type' => 'image/x-icon',
                'Cache-Control' => 'public, max-age=31536000', // cache for 1 year
            ]);
        })->name('mle.favicon');
    });
}
