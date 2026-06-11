<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Mlbrgn\LaravelFormComponents\Providers\FormComponentsServiceProvider;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\DemoController;

class BrowserTestCase extends TestCase
{
    public function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('medialibrary-extensions.demo_pages_enabled', true);
        $app['config']->set('medialibrary-extensions.route_middleware', ['web']);

        $app['config']->set('filesystems.disks.media_demo', [
            'driver' => 'local',
            'root' => $this->getTempDirectory().'/media_demo',
            'url' => '/media_demo',
        ]);

        $app['config']->set('filesystems.disks.media_originals', [
            'driver' => 'local',
            'root' => $this->getTempDirectory().'/media_originals',
            'url' => '/media_originals',
        ]);

        $sessionPath = $this->getTempDirectory().'/sessions';

        File::ensureDirectoryExists($sessionPath);

        $app['config']->set('session.driver', 'file');
        $app['config']->set('session.files', $sessionPath);

        $app['config']->set('session.lottery', [2, 100]);
        $app['config']->set('session.expire_on_close', false);

        $app['config']->set('session.cookie', 'laravel_session');
        $app['config']->set('session.domain', null);
        $app['config']->set('session.secure', false);
        $app['config']->set('session.same_site', 'lax');
        $app['config']->set('session.http_only', true);
        $app['config']->set('session.secure', false);

        // Register routes for the browser test
        Route::get('/media_originals/{path}', function (string $path) {
            $root = realpath(config('filesystems.disks.media_originals.root'));

            if ($root === false) {
                abort(404);
            }

            $file = realpath($root.'/'.$path);

            if ($file === false || ! str_starts_with($file, $root.DIRECTORY_SEPARATOR) || ! is_file($file)) {
                abort(404);
            }

            return response()->file($file);
        })->where('path', '.*');
        Route::middleware('web')->group(function () {
            Route::get('mle-demo', DemoController::class)->name('mle-demo');
            Route::get('mle-theme-switch', fn () => redirect()->back())->name('mlbrgn.mle.theme-switch');

            Route::get('/media_demo/{id}/{filename}', function ($id, $filename) {
                return response()->file(config('filesystems.disks.media_demo.root').'/'.$id.'/'.$filename);
            });

            Route::get('/vendor/mlbrgn/{package}/{type}/{path}', function ($package, $type, $path) {
                $baseDistPath = realpath(__DIR__.'/../dist');
                $filePath = $baseDistPath.'/'.$type.'/'.$path;

                if (! $baseDistPath || ! str_starts_with(realpath($filePath) ?: '', $baseDistPath)) {
                    abort(403);
                }

                if (! file_exists($filePath)) {
                    abort(404);
                }

                $mimeType = match ($type) {
                    'css' => 'text/css',
                    'js' => 'application/javascript',
                    default => File::mimeType($filePath),
                };

                return response()->file($filePath, [
                    'Content-Type' => $mimeType,
                ]);
            })->where('path', '.*');
        });
        Route::get('favicon.ico', fn () => '')->name('mlbrgn.mle.favicon');
    }

    protected function getPackageProviders($app): array
    {
        $providers = parent::getPackageProviders($app);

        if (class_exists(FormComponentsServiceProvider::class)) {
            $providers[] = FormComponentsServiceProvider::class;
        }

        return $providers;
    }

    public function getMediaInput(string $id): string
    {
        return "@media-input-$id";
    }

    public function getUploadButton(string $id): string
    {
        return "@upload-button-$id";
    }

    public function getPreviewGrid(string $id): string
    {
        return "[data-test=\"media-preview-grid-$id\"]";
    }

    public function getMenuEnd(string $id): string
    {
        return "[data-test=\"media-preview-menu-end-$id\"]";
    }

    public function getMenuButton(string $id): string
    {
        return '[data-test="media-preview-menu-button"]';
    }

    public function assertPreviewImageVisible($page, string $id): void
    {
        $grid = $this->getPreviewGrid($id);
        $page->assertPresent("$grid [data-test=\"media-preview-item\"]:first-child")
            ->assertPresent("$grid [data-test=\"media-preview-item\"]:first-child [data-test=\"media-preview-image\"]");
    }
}
