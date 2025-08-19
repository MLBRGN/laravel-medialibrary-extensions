<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Exception;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManager;

beforeEach(function () {
    // Stub necessary config
    Config::set('media-library-extensions.allowed_mimes.image', ['image/jpeg', 'image/png']);
    Config::set('media-library-extensions.frontend_theme', 'bootstrap-5');
    Config::set('media-library-extensions.use_xhr', true);
    Config::set('media-library-extensions.upload_field_name_single', 'media_single');
    Config::set('media-library-extensions.upload_field_name_multiple', 'media_multiple');
});

test('media manager component renders', function () {

    $model = $this->getTestBlogModel();

    $html = Blade::render('<x-mle-media-manager
        :model-or-class-name="$model"
        image-collection="blog-images"
        youtube-collection="blog-youtube"
        document-collection="blog-documents"
        upload-enabled
        destroy-enabled
        set-as-first-enabled
        show-media-url
        show-order
        id="blog"
        multiple
    />', [
        'model' => $model,
    ]);

//    dd($html);
    expect($html)
        ->toContain('blog-mmm')
        ->toContain('Mlbrgn\\MediaLibraryExtensions\\Tests\\Models\\Blog')
        ->toContain('media_multiple')
        ->toContain('csrf_token');
});

it('initializes without temporary upload when a eloquent model is provided', function () {
    config()->set('media-library-extensions.demo_pages_enabled', false);
    $model = Blog::create(['title' => 'test']);
    $component = new MediaManager(modelOrClassName: $model, multiple: true, imageCollection: 'blog-main');

    expect($component->model)->not()->toBeNull()
        ->and($component->modelType)->toBe('Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog')
        ->and($component->modelId)->not()->toBeNull()
        ->and($component->temporaryUpload)->toBeFalse()
        ->and($component->mediaUploadRoute)->toBe(URL::route(mle_prefix_route('media-upload-multiple')))
        ->and($component->uploadFieldName)->toBe('media_multiple');
});

it('initializes with temporary upload when only model class name provided', function () {
    $component = new MediaManager(modelOrClassName: 'App\\Models\\Post', multiple: true, imageCollection: 'blog-main');

    expect($component->model)->toBeNull()
        ->and($component->modelType)->toBe('App\\Models\\Post')
        ->and($component->modelId)->toBeNull()
        ->and($component->temporaryUpload)->toBeTrue()
        ->and($component->mediaUploadRoute)->toBe(URL::route(mle_prefix_route('media-upload-multiple')))
        ->and($component->uploadFieldName)->toBe('media_multiple');
});

// it('throws if modelOrClassName is invalid type', function () {
//    new MediaManager(modelOrClassName: new stdClass());
// })->throws(Exception::class, 'model-or-class-name must be either a HasMedia model or a string representing the model class');
