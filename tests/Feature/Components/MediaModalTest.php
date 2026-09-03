<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaModal;

it('appends -mod to the id', function () {
    $model = $this->getTestBlogModel();
    $component = new MediaModal(
        id: 'media1',
        modelReference: $model,
        collections: ['images'],
        title: 'ID Test'
    );

    expect($component->getDomId())->toBe('media1-mod');
});

it('returns the correct view on render', function () {
    $model = $this->getTestBlogModel();
    $component = new MediaModal(
        id: 'test-media-modal',
        modelReference: $model,
        collections: ['images'],
        title: 'Render Test'
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class)
        ->and($view->name())->toBe('medialibrary-extensions::components.bootstrap-5.media-modal');
});

it('renders the correct Blade view (bootstrap-5)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);
    $theme = 'bootstrap-5';
    $component = new MediaModal(
        id: 'test-media-modal',
        modelReference: $model,
        collections: ['image_collection'],
        title: 'Render Test',
        options: [
            'theme' => $theme,
        ]
    );
    $view = $component->render();
    //    dd($view);
    expect($view->name())->toBe('medialibrary-extensions::components.bootstrap-5.media-modal');
});

it('renders the correct Blade view (plain)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);
    $theme = 'plain';
    $component = new MediaModal(
        id: 'test-media-modal',
        modelReference: $model,
        collections: ['image_collection'],
        title: 'Render Test',
        options: [
            'theme' => $theme,
        ]
    );
    $view = $component->render();
    expect($view->name())->toBe('medialibrary-extensions::components.plain.media-modal');

});

it('renders the correct html multiple collections (plain)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-modal
                    id="test-media-modal"
                    :model-reference="$model"
                    :collections="$collections"
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
                'youtube_collection',
            ],
            'options' => [
                'theme' => 'plain',
            ],
        ]
    );
    $html = preg_replace('/\?v=\d+/', '', $html);
    expect($html)->toMatchSnapshot();
});

it('renders the correct html multiple collections (bootstrap-5)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-modal
                    id="test-media-modal"
                    :model-reference="$model"
                    :collections="$collections"
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
                'youtube_collection',
            ],
            'options' => [
                'theme' => 'bootstrap-5',
            ],
        ]
    );
    $html = preg_replace('/\?v=\d+/', '', $html);
    expect($html)->toMatchSnapshot();
});

it('renders the correct html single collection (plain)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-modal
                id="test-media-modal"
                :model-reference="$model"
                :collections="$collections"
                title="test"
                :options="$options"
            />',
        [
            'model' => $model,
            'collections' => ['image_collection'],
            'options' => [
                'theme' => 'plain',
            ],
        ]
    );
    $html = preg_replace('/\?v=\d+/', '', $html);
    expect($html)->toMatchSnapshot();
});

it('renders the correct html single collection (bootstrap-5)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-modal
                    id="test-media-modal"
                    :model-reference="$model"
                    :collections="$collections"
                    title="test"
                    :options="$options"
                 />',
        [
            'model' => $model,
            'collections' => ['image_collection'],
            'options' => [
                'theme' => 'bootstrap-5',
            ],
        ]
    );
    $html = preg_replace('/\?v=\d+/', '', $html);
    expect($html)->toMatchSnapshot();
});

it('sets temporary upload mode when given a class string', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $component = new MediaModal(
        id: 'test-media-modal',
        modelReference: $model->getMorphClass(),
        collections: ['images'],
        title: 'Temp Upload'
    );

    expect($component->temporaryUploadMode)->toBeTrue()
        ->and($component->model)->toBeNull()
        ->and($component->modelType)->toBe(get_class($model));
});

it('throws if given class string does not exist', function () {
    $modelReference = 'NonExistent\Model';
    expect(fn () => new MediaModal(
        id: 'test-media-modal',
        modelReference: $modelReference,
        collections: null,
        title: 'Invalid'
    ))->toThrow(\Mlbrgn\MediaLibraryExtensions\Exceptions\InvalidModelTypeException::class, __('medialibrary-extensions::messages.class_does_not_exist', [
        'class_name' => $modelReference,
    ]));
});

it('throws if given class string does not implement HasMediaExtended', function () {
    $modelReference = \stdClass::class;
    expect(fn () => new MediaModal(
        id: 'test-media-modal',
        modelReference: $modelReference,
        collections: null,
        title: 'Invalid'
    ))->toThrow(\Mlbrgn\MediaLibraryExtensions\Exceptions\InvalidModelTypeException::class, __('medialibrary-extensions::messages.must_implement_has_media', [
        'class' => $modelReference,
        'interface' => \Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended::class,
    ]));
});
