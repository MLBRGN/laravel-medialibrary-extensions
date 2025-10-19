<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaModal;
use Spatie\MediaLibrary\HasMedia;

it('appends -mod to the id', function () {
    $model = $this->getTestBlogModel();
    $component = new MediaModal(
        id: 'media1',
        modelOrClassName: $model,
        mediaCollection: 'images',
        mediaCollections: null,
        title: 'ID Test'
    );

    expect($component->id)->toBe('media1-mod');
});

it('returns the correct view on render', function () {
    $model = $this->getTestBlogModel();
    $component = new MediaModal(
        id: 'test-media-modal',
        modelOrClassName: $model,
        mediaCollection: 'images',
        mediaCollections: null,
        title: 'Render Test'
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class)
        ->and($view->name())->toBe('media-library-extensions::components.bootstrap-5.media-modal');
});

it('renders the correct Blade view (bootstrap-5)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);
    $frontendTheme = 'bootstrap-5';
    $component = new MediaModal(
        id: 'test-media-modal',
        modelOrClassName: $model,
        mediaCollection: 'image_collection',
        mediaCollections: null,
        title: 'Render Test',
        options: [
            'frontendTheme' => $frontendTheme
        ]
    );
    $view = $component->render();
    //    dd($view);
    expect($view->name())->toBe('media-library-extensions::components.bootstrap-5.media-modal');
});

it('renders the correct Blade view (plain)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);
    $frontendTheme = 'plain';
    $component = new MediaModal(
        id: 'test-media-modal',
        modelOrClassName: $model,
        mediaCollection: 'image_collection',
        mediaCollections: null,
        title: 'Render Test',
        options: [
            'frontendTheme' => $frontendTheme
        ]
    );
    $view = $component->render();
    expect($view->name())->toBe('media-library-extensions::components.plain.media-modal');

});

it('renders the correct html multiple media-collections (plain)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-modal
                    id="test-media-modal"
                    :model-or-class-name="$model"
                    :media-collections="$collections"
                    title="test"
                    :options="$options"
                />',
        [
            'model' => $model,
            'collections' => [
                'image_collection',
                'document_collection',
                'video_collection',
                'audio_collection',
                'youtube_collection'
            ],
            'options' => [
                'frontendTheme' => 'plain',
            ]
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('renders the correct html multiple media-collections (bootstrap-5)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-modal
                    id="test-media-modal"
                    :model-or-class-name="$model"
                    :media-collections="$collections"
                    title="test"
                    :options="$options"
                />',
        [
            'model' => $model,
            'collections' => [
                'image_collection',
                'document_collection',
                'video_collection',
                'audio_collection',
                'youtube_collection'
            ],
            'options' => [
                'frontendTheme' => 'bootstrap-5',
            ]
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('renders the correct html single media-collection (plain)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-modal
                id="test-media-modal"
                :model-or-class-name="$model"
                :media-collection="$collection"
                title="test"
                :options="$options"
            />',
        [
            'model' => $model,
            'collection' => 'image_collection',
            'options' => [
                'frontendTheme' => 'plain',
            ]
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('renders the correct html single media-collection (bootstrap-5)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-modal
                    id="test-media-modal"
                    :model-or-class-name="$model"
                    :media-collection="$collection"
                    title="test"
                    :options="$options"
                 />',
        [
            'model' => $model,
            'collection' => 'image_collection',
            'options' => [
                'frontendTheme' => 'bootstrap-5',
            ]
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('sets temporary upload mode when given a class string', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $component = new MediaModal(
        id: 'test-media-modal',
        modelOrClassName: $model->getMorphClass(),
        mediaCollection: 'images',
        mediaCollections: null,
        title: 'Temp Upload'
    );

    expect($component->temporaryUploadMode)->toBeTrue()
        ->and($component->model)->toBeNull()
        ->and($component->modelType)->toBe($model->getMorphClass());
});

it('throws if given class string does not exist', function () {
    $modelOrClassName = 'NonExistent\Model';
    expect(fn () => new MediaModal(
        id: 'test-media-modal',
        modelOrClassName: $modelOrClassName,
        mediaCollection: null,
        mediaCollections: null,
        title: 'Invalid'
    ))->toThrow(\InvalidArgumentException::class, __('media-library-extensions::messages.class_does_not_exist', [
        'class_name' => $modelOrClassName,
    ]));
});

it('throws if given class string does not implement HasMedia', function () {
    $modelOrClassName = \stdClass::class;
    expect(fn () => new MediaModal(
        id: 'test-media-modal',
        modelOrClassName: $modelOrClassName,
        mediaCollection: null,
        mediaCollections: null,
        title: 'Invalid'
    ))->toThrow(\UnexpectedValueException::class, __('media-library-extensions::messages.must_implement_has_media', [
        'class' => $modelOrClassName,
        'interface' => HasMedia::class,
    ]));
});
