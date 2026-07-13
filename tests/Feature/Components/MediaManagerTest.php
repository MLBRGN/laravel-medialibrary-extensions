<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Exception;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManager;

beforeEach(function () {
    // Stub necessary config
    Config::set('medialibrary-extensions.allowed_mimes.image', ['image/jpeg', 'image/png']);
    Config::set('medialibrary-extensions.frontend_theme', 'bootstrap-5');
    Config::set('medialibrary-extensions.use_xhr', true);
    Config::set('medialibrary-extensions.upload_field_name', 'media');
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
                'theme' => 'bootstrap-5',
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
        ->toContain('csrf_token');
});

it('appends correct id suffix based on multiple flag', function () {
    $model = $this->getTestBlogModel();

    // multiple = true → should append "-mmm"
    $componentMultiple = new MediaManager(
        id: 'my-media',
        modelOrClassName: $model,
        collections: ['image' => 'blog-images'],
        multiple: true,
    );

    // multiple = false → should append "-mms"
    $componentSingle = new MediaManager(
        id: 'my-media',
        modelOrClassName: $model,
        collections: ['image' => 'blog-images'],
        multiple: false,
    );

    expect($componentMultiple->getDomId())->toBe('my-media-mmm')
        ->and($componentSingle->getDomId())->toBe('my-media-mms');
});

it('initializes without temporary upload when a eloquent model is provided', function () {
    config()->set('medialibrary-extensions.demo_pages_enabled', false);
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
            'theme' => 'bootstrap-5',
            'showMediaEditButton' => true,
        ],
        multiple: true,
    );

    expect($component->model)->not()->toBeNull()
        ->and($component->modelType)->toBe('Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog')
        ->and($component->modelId)->not()->toBeNull()
        ->and($component->getConfig('temporaryUploadMode'))->toBeFalse()
        ->and($component->getConfig('routes.mediaUpload'))->toBe(URL::route(mle_prefix_route('media-upload-multiple')));
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
            'theme' => 'bootstrap-5',
            'showMediaEditButton' => true,
        ],
        multiple: true,

    );

    expect($component->model)->toBeNull()
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->modelId)->toBeNull()
        ->and($component->getConfig('temporaryUploadMode'))->toBeTrue()
        ->and($component->getConfig('routes.mediaUpload'))->toBe(URL::route(mle_prefix_route('media-upload-multiple')));
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
                'theme' => 'plain',
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
    $html = $this->sanitizeHtmlSnapshot($html);

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
                'theme' => 'bootstrap-5',
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
    $html = $this->sanitizeHtmlSnapshot($html);

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
                'theme' => 'plain',
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
    $html = $this->sanitizeHtmlSnapshot($html);

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
                'theme' => 'bootstrap-5',
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
    $html = $this->sanitizeHtmlSnapshot($html);
    expect($html)->toMatchSnapshot();
});

it('throws if given class string does not exist', function () {
    $modelOrClassName = 'NonExistent\Model';
    expect(fn () => new MediaManager(
        id: 'test-media-manager',
        modelOrClassName: $modelOrClassName,
        collections: ['image' => 'blog-main'],
    ))->toThrow(\InvalidArgumentException::class, __('medialibrary-extensions::messages.class_does_not_exist', [
        'class_name' => $modelOrClassName,
    ]));
});

it('throws if given class string does not implement HasMediaExtended', function () {
    $modelOrClassName = \stdClass::class;
    expect(fn () => new MediaManager(
        id: 'test-media-manager',
        modelOrClassName: $modelOrClassName,
        collections: ['image' => 'blog-main'],
    ))->toThrow(\UnexpectedValueException::class, __('medialibrary-extensions::messages.must_implement_has_media', [
        'class' => $modelOrClassName,
        'interface' => HasMediaExtended::class,
    ]));
});

it('disables showSetAsFirstButton when multiple is false', function () {
    $model = $this->getTestBlogModel();

    $component = new MediaManager(
        id: 'test-single',
        modelOrClassName: $model,
        collections: ['image' => 'blog-images'],
        options: ['showSetAsFirstButton' => true],
        multiple: false,
    );

    expect($component->getConfig('showSetAsFirstButton'))->toBeFalse();
});

it('throws exception when no collections provided', function () {
    $model = $this->getTestBlogModel();

    expect(fn () => new MediaManager(
        id: 'test-empty-collections',
        modelOrClassName: $model,
        collections: [],
    ))->toThrow(Exception::class, __('medialibrary-extensions::messages.no_media_collections'));
});

it('disables upload form when no uploadable collections exist', function () {
    $model = $this->getTestBlogModel();

    $component = new MediaManager(
        id: 'test-no-uploadables',
        modelOrClassName: $model,
        collections: ['youtube' => 'blog-youtube'], // only YouTube
        options: ['showUploadForm' => true],
        multiple: true,
    );

    expect($component->getConfig('showUploadForm'))->toBeFalse();
});

it('disables YouTube upload form when youtube collection missing', function () {
    $model = $this->getTestBlogModel();

    $component = new MediaManager(
        id: 'test-no-youtube',
        modelOrClassName: $model,
        collections: ['image' => 'blog-images'],
        options: ['showYouTubeUploadForm' => true],
        multiple: true,
    );

    expect($component->getConfig('showYouTubeUploadForm'))->toBeFalse();
});

it('hides media menu when all menu buttons disabled', function () {
    $model = $this->getTestBlogModel();

    $component = new MediaManager(
        id: 'test-hide-menu',
        modelOrClassName: $model,
        collections: ['image' => 'blog-images'],
        options: [
            'showDestroyButton' => false,
            'showSetAsFirstButton' => false,
            'showMediaEditButton' => false,
        ],
        multiple: true,
    );

    expect($component->getConfig('showMenu'))->toBeFalse();
})->todo('fix this test');

it('does not leak model', function () {
    $model = $this->getTestBlogModel();
    $component = new MediaManager(
        id: 'abc',
        modelOrClassName: $model,
        collections: ['image' => 'images'],
        options: [
        ],
        multiple: false
    );

    expect($component->getConfig())
        ->not->toHaveKeys([
            'model',
            'modelOrClassName',
            'singleMedia',
        ]);
});
