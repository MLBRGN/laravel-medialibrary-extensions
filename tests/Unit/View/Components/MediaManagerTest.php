<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManager;
use Spatie\MediaLibrary\HasMedia;

beforeEach(function () {
    // Stub necessary config
    Config::set('media-library-extensions.allowed_mimes.image', ['image/jpeg', 'image/png']);
    Config::set('media-library-extensions.frontend_theme', 'bootstrap-5');
    Config::set('media-library-extensions.use_xhr', true);
    Config::set('media-library-extensions.upload_field_name_single', 'media_single');
    Config::set('media-library-extensions.upload_field_name_multiple', 'media_multiple');
});

it('renders media manager component', function () {
    $model = $this->getTestBlogModel();

    $html = Blade::render(<<<'BLADE'
        <x-mle-media-manager
            id="blog"
            :model-or-class-name="$model"
            :collections="[
                'image' => 'blog-images',
                'youtube-collection' => 'blog-youtube',
                'document' => 'blog-documents',
            ]"
            :options="[
                'showDestroyButton' => true,
                'showSetAsFirstButton' => true,
                'showOrder' => true,
                'useXhr' => true,
                'showUploadForm' => true,
                'allowedMimeTypes' => 'image/jpeg, image/png',
                'frontendTheme' => 'bootstrap-5',
                'showMediaEditButton' => true,
            ]"
            multiple
        />
    BLADE, [
        'model' => $model,
    ]);

    expect($html)
        ->toContain('blog-mmm')
        ->toContain('Mlbrgn\\MediaLibraryExtensions\\Tests\\Models\\Blog')
        ->toContain('media_multiple')
        ->toContain('csrf_token');
});

it('initializes without temporary upload when a eloquent model is provided', function () {
    config()->set('media-library-extensions.demo_pages_enabled', false);
    $model = Blog::create(['title' => 'test']);
    $component = new MediaManager(
        id: 'test-media-manager',
        modelOrClassName: $model,
        collections: ['image' => 'blog-main'],
        options: [
            'showDestroyButton' => true,
            'showSetAsFirstButton' => true,
            'showOrder' => true,
            'useXhr' => true,
            'showUploadForm' => true,
            'allowedMimeTypes' => 'image/jpeg, image/png',
            'frontendTheme' => 'bootstrap-5',
            'showMediaEditButton' => true,
        ],
        multiple: true,
    );

    expect($component->model)->not()->toBeNull()
        ->and($component->modelType)->toBe('Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog')
        ->and($component->modelId)->not()->toBeNull()
        ->and($component->getConfig('temporaryUploadMode'))->toBeFalse()
        ->and($component->getConfig('mediaUploadRoute'))->toBe(URL::route(mle_prefix_route('media-upload-multiple')))
        ->and($component->getConfig('uploadFieldName'))->toBe('media_multiple');
});

it('initializes with temporary upload when only model class name provided', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);
    $component = new MediaManager(
        id: 'test-media-manager',
        modelOrClassName: $model->getMorphClass(),
        collections: ['image' => 'blog-main'],
        options: [
            'showDestroyButton' => true,
            'showSetAsFirstButton' => true,
            'showOrder' => true,
            'useXhr' => true,
            //            'showUploadForm' => true,
            //            'allowedMimeTypes' => 'image/jpeg, image/png',
            'frontendTheme' => 'bootstrap-5',
            'showMediaEditButton' => true,
        ],
        multiple: true,

    );

    expect($component->model)->toBeNull()
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->modelId)->toBeNull()
        ->and($component->getConfig('temporaryUploadMode'))->toBeTrue()
        ->and($component->getConfig('mediaUploadRoute'))->toBe(URL::route(mle_prefix_route('media-upload-multiple')))
        ->and($component->getConfig('uploadFieldName'))->toBe('media_multiple');
});

it('renders the correct html multiple (plain)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-manager
        id="test-media-modal"
        :model-or-class-name="$modelOrClassName"
        :collections="$collections"
        :options="$options"
        multiple="true"
    />',
        [
            'modelOrClassName' => $model,
            'collections' => [
                'image' => 'blog-images',
                'youtube' => 'blog-youtube',
                'document' => 'blog-documents',
                'video' => 'blog-videos',
                'audio' => 'blog-audio',
            ],
            'options' => [
                'frontendTheme' => 'plain',
                'showSetAsFirstButton' => true,
                'showOrder' => true,
                'showDestroyButton' => true,
                'useXhr' => true,
                'showUploadForm' => true,
                'allowedMimeTypes' => 'image/jpeg, image/png',
                'showMediaEditButton' => true,
            ],
        ]
    );

    expect($html)->toMatchSnapshot();
});

it('renders the correct html multiple (bootstrap-5)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-manager
        id="test-media-modal"
        :model-or-class-name="$modelOrClassName"
        :collections="$collections"
        :options="$options"
        multiple="true"
    />',
        [
            'modelOrClassName' => $model,
            'collections' => [
                'image' => 'blog-images',
                'youtube' => 'blog-youtube',
                'document' => 'blog-documents',
                'video' => 'blog-videos',
                'audio' => 'blog-audio',
            ],
            'options' => [
                'frontendTheme' => 'bootstrap-5',
                'showSetAsFirstButton' => true,
                'showOrder' => true,
                'showDestroyButton' => true,
                'useXhr' => true,
                'showUploadForm' => true,
                'allowedMimeTypes' => 'image/jpeg, image/png',
                'showMediaEditButton' => true,
            ],
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('renders the correct html single (plain)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-manager
        id="test-media-modal"
        :model-or-class-name="$modelOrClassName"
        :collections="$collections"
        :options="$options"
        multiple="false"
    />',
        [
            'modelOrClassName' => $model,
            'collections' => [
                'image' => 'blog-images',
                'youtube' => 'blog-youtube',
                'document' => 'blog-documents',
                'video' => 'blog-videos',
                'audio' => 'blog-audio',
            ],
            'options' => [
                'frontendTheme' => 'plain',
                'showSetAsFirstButton' => true,
                'showOrder' => true,
                'showDestroyButton' => true,
                'useXhr' => true,
                'showUploadForm' => true,
                'allowedMimeTypes' => 'image/jpeg, image/png',
                'showMediaEditButton' => true,
            ],
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('renders the correct html single (bootstrap-5)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-manager
        id="test-media-modal"
        :model-or-class-name="$modelOrClassName"
        :collections="$collections"
        :options="$options"
        multiple="false"
    />',
        [
            'modelOrClassName' => $model,
            'collections' => [
                'image' => 'blog-images',
                'youtube' => 'blog-youtube',
                'document' => 'blog-documents',
                'video' => 'blog-videos',
                'audio' => 'blog-audio',
            ],
            'options' => [
                'frontendTheme' => 'bootstrap-5',
                'showSetAsFirstButton' => true,
                'showOrder' => true,
                'showDestroyButton' => true,
                'useXhr' => true,
                'showUploadForm' => true,
                'allowedMimeTypes' => 'image/jpeg, image/png',
                'showMediaEditButton' => true,
            ],
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('throws if given class string does not exist', function () {
    $modelOrClassName = 'NonExistent\Model';
    expect(fn () => new MediaManager(
        id: 'test-media-manager',
        modelOrClassName: $modelOrClassName,
        collections: ['image' => 'blog-main'],
    ))->toThrow(\InvalidArgumentException::class, __('media-library-extensions::messages.class_does_not_exist', [
        'class_name' => $modelOrClassName,
    ]));
});

it('throws if given class string does not implement HasMedia', function () {
    $modelOrClassName = \stdClass::class;
    expect(fn () => new MediaManager(
        id: 'test-media-manager',
        modelOrClassName: $modelOrClassName,
        collections: ['image' => 'blog-main'],
    ))->toThrow(\UnexpectedValueException::class, __('media-library-extensions::messages.must_implement_has_media', [
        'class' => $modelOrClassName,
        'interface' => HasMedia::class,
    ]));
});
