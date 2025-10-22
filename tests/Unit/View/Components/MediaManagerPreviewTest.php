<?php

use Illuminate\Support\Collection;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManager;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerPreview;

beforeEach(function () {
    // Mock TemporaryUpload::forCurrentSession static method for tests using temporary uploads
    //    TemporaryUpload::shouldReceive('forCurrentSession')->andReturn(collect());
});

it('initializes correctly with model instance', function () {

    $model = $this->getTestBlogModel();
    $component = new MediaManagerPreview(
        id: 'blog-1',
        modelOrClassName: $model,
        collections: [
            'image' => 'images',
        ],
        options: [
            'showUploadForm' => true,
            'showDestroyButton' => true,
            'showOrder' => true,
        ]
    );

    expect($component->options['showUploadForm'])->toBeTrue()
        ->and($component->options['showDestroyButton'])->toBeTrue()
        ->and($component->options['showOrder'])->toBeTrue()
        ->and($component->collections)
        ->toHaveKey('image', 'images')
        ->and($component->id)->toBe('blog-1');
});

it('accepts a HasMedia model instance and sets properties accordingly', function () {
    $model = $this->getTestBlogModel();

    $component = new MediaManagerPreview(
        id: 'test',
        modelOrClassName: $model,
        collections: [
            'image' => 'images',
            'document' => 'docs',
            'youtube' => 'videos',
        ]
    );

    expect($component->model)->toBe($model)
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->modelId)->toBe($model->id)
        ->and($component->temporaryUploadMode)->toBeFalse()
        ->and($component->media)->toBeInstanceOf(Collection::class);
});

it('accepts a string model class name and sets temporaryUpload to true', function () {
    $model = $this->getTestBlogModel();

    $component = new MediaManagerPreview(
        id: 'mediaManagerPreviewTest',
        modelOrClassName: $model->getMorphClass()
    );

    expect($component->model)->toBeNull()
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->modelId)->toBeNull()
        ->and($component->temporaryUploadMode)->toBeTrue();
});

it('throws exception if modelOrClassName is invalid type', function () {
    new MediaManagerPreview(modelOrClassName: 12345);
})->throws(Exception::class, 'model-or-class-name must be either a HasMedia model or a string representing the model class')->todo();

it('sets showMenu to true if showDestroyButton, showOrder or showSetAsFirstButton or showMediaEditButton are true', function () {
    $model = $this->getTestBlogModel();
    foreach ([['showDestroyButton' => true], ['showOrder' => true], ['showSetAsFirstButton' => true]] as $flags) {
        $component = new MediaManagerPreview(
            id: 'mediaManagerPreviewTest',
            modelOrClassName: $model,
            options: [
                'showDestroyButton' => $flags['showDestroyButton'] ?? false,
                'showSetAsFirstButton' => $flags['showSetAsFirstButton'] ?? false,
                'showOrder' => $flags['showOrder'] ?? false,
            ]
        );

        expect($component->model)->toBe($model)
            ->and($component->modelType)->toBe($model->getMorphClass())
            ->and($component->modelId)->toBe($model->id)
            ->and($component->id)->toBe('mediaManagerPreviewTest');
        //            ->and($component->getConfig('showMenu'))->toBeTrue();
    }
});

it('hides media menu when all menu buttons disabled', function () {
    $model = $this->getTestBlogModel();

    $component = new MediaManagerPreview(
        id: 'test-hide-menu',
        modelOrClassName: $model,
        collections: ['image' => 'blog-images'],
        options: [
            'showDestroyButton' => false,
            'showSetAsFirstButton' => false,
            'showMediaEditButton' => false,
        ],
    );

    expect($component->getConfig('showMenu'))->toBeFalse();
});

it('merges media from model collections correctly', function () {

    $model = $this->getModelWithMedia([
        'image' => 2,
        'document' => 2,
        'audio' => 2,
        'video' => 2,
    ]);

    $component = new MediaManagerPreview(
        id: 'mediaManagerPreviewTest',
        modelOrClassName: $model,
        collections: [
            'image' => 'image_collection',
            'document' => 'document_collection',
            'audio' => 'audio_collection',
            'video' => 'video_collection',
            //            'youtube' => 'youtube_video_collection',
        ]
    );

    expect($component->media)->toBeInstanceOf(Collection::class)
        ->and($component->media->count())->toBe(8);
});

it('merges temporary uploads when temporaryUploads is true', function () {
    // Mock TemporaryUpload::forCurrentSession to return collections with different counts
    TemporaryUpload::shouldReceive('forCurrentSession')
        ->with('images')
        ->andReturn(collect(['img1', 'img2']));
    TemporaryUpload::shouldReceive('forCurrentSession')
        ->with('youtube')
        ->andReturn(collect(['vid1']));
    TemporaryUpload::shouldReceive('forCurrentSession')
        ->with('documents')
        ->andReturn(collect(['doc1', 'doc2', 'doc3']));

    $component = new MediaManagerPreview(
        id: 'mediaManagerPreviewTest',
        modelOrClassName: 'App\Models\DummyClass',
        collections: [
            'image' => 'images',
            'document' => 'documents',
            'youtube' => 'youtube',
        ],
        options: [
            'temporaryUploadMode' => true,
        ]
    );

    expect($component->media)->toBeInstanceOf(Collection::class)
        ->and($component->media->count())->toBe(6);
})->todo();

it('returns the correct view', function () {

    $model = $this->getTestBlogModel();

    $component = new MediaManagerPreview(
        id: 'mediaManagerPreviewTest',
        modelOrClassName: $model
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(Illuminate\View\View::class);
    expect($view->name())->toBe('media-library-extensions::components.bootstrap-5.media-manager-preview');
});

it('returns the correct view when only class name provided', function () {
    $component = new MediaManagerPreview(
        id: 'mediaManagerPreviewTest',
        modelOrClassName: Blog::class,
    );

    $view = $component->render();

    expect($component->getConfig('showDestroyButton'))->toBeTrue()
        ->and($component->getConfig('showSetAsFirstButton'))->toBeTrue()
        ->and($component->getConfig('showMediaEditButton'))->toBeTrue()
        ->and($component->getConfig('showOrder'))->toBeFalse()
        ->and($component->getConfig('temporaryUploadMode'))->toBeTrue();
    //        ->and($component->frontendTheme)->toBe('bootstrap-5');
    expect($view)->toBeInstanceOf(Illuminate\View\View::class);
    expect($view->name())->toBe('media-library-extensions::components.bootstrap-5.media-manager-preview');
});

it('renders view and matches snapshot (plain)', function () {
    $model = $this->getModelWithMedia([
        'image' => 2,
        'document' => '1',
        'audio' => 1,
        'video' => 1,
    ]);

    $html = Blade::render(
        '<x-mle-media-manager-preview
                    id="test-media-modal"
                    :model-or-class-name="$modelOrClassName"
//                    image_collection="images"
                    :options="$options"
                />',
        [
            'modelOrClassName' => $model,
            'options' => [
                'frontendTheme' => 'plain',
            ],
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('renders view and matches snapshot (bootstrap-5)', function () {
    $model = $this->getModelWithMedia([
        'image' => 2,
        'document' => '1',
        'audio' => 1,
        'video' => 1,
    ]);

    $html = Blade::render(
        '<x-mle-media-manager-preview
                    id="test-media-modal"
                    :model-or-class-name="$modelOrClassName"
//                    image_collection="images"
                    :options="$options"
                />',
        [
            'modelOrClassName' => $model,
            'options' => [
                'frontendTheme' => 'bootstrap-5',
            ],
        ]
    );
    expect($html)->toMatchSnapshot();
});
