<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Exception;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManager;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaModal;
use Spatie\MediaLibrary\HasMedia;
use function Livewire\on;

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
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);
    $component = new MediaManager(modelOrClassName: $model->getMorphClass(), multiple: true, imageCollection: 'blog-main');

    expect($component->model)->toBeNull()
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->modelId)->toBeNull()
        ->and($component->temporaryUpload)->toBeTrue()
        ->and($component->mediaUploadRoute)->toBe(URL::route(mle_prefix_route('media-upload-multiple')))
        ->and($component->uploadFieldName)->toBe('media_multiple');
});

it('renders the correct html multiple (plain)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-manager id="test-media-modal" :model-or-class-name="$modelOrClassName" image_collection="images" :frontend-theme="$frontendTheme" multiple="true"/>',
        [
            'modelOrClassName' => $model,
            'frontendTheme' => 'plain'
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('renders the correct html multiple (bootstrap-5)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-manager id="test-media-modal" :model-or-class-name="$modelOrClassName" image_collection="images" :frontend-theme="$frontendTheme" multiple="true" />',
        [
            'modelOrClassName' => $model,
            'frontendTheme' => 'plain'
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('renders the correct html single (plain)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-manager id="test-media-modal" :model-or-class-name="$modelOrClassName" image_collection="images" :frontend-theme="$frontendTheme" multiple="false"/>',
        [
            'modelOrClassName' => $model,
            'frontendTheme' => 'plain'
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('renders the correct html single (bootstrap-5)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-manager id="test-media-modal" :model-or-class-name="$modelOrClassName" image_collection="images" :frontend-theme="$frontendTheme" multiple="false"/>',
        [
            'modelOrClassName' => $model,
            'frontendTheme' => 'plain'
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('throws if given class string does not exist', function () {
    $modelOrClassName = 'NonExistent\Model';
    expect(fn () => new MediaManager(
        modelOrClassName: $modelOrClassName,
        imageCollection: 'blog-main',
    ))->toThrow(\InvalidArgumentException::class, __('media-library-extensions::messages.class_does_not_exist', [
        'class_name' => $modelOrClassName
    ]));
});

it('throws if given class string does not implement HasMedia', function () {
    expect(fn () => new MediaManager(
        modelOrClassName: \stdClass::class,
        imageCollection: 'blog-main',
    ))->toThrow(\InvalidArgumentException::class, __('media-library-extensions::messages.class_must_implement', [
        'class_name' => HasMedia::class
    ]));
});
