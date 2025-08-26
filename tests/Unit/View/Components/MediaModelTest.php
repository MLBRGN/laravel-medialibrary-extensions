<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaModal;
use Spatie\MediaLibrary\HasMedia;
use function Livewire\on;

it('appends -mod to the id', function () {
    $model = $this->getTestBlogModel();
    $component = new MediaModal(
        modelOrClassName: $model,
        mediaCollection: 'images',
        mediaCollections: null,
        title: 'ID Test',
        id: 'media1'
    );

    expect($component->id)->toBe('media1-mod');
});

it('returns the correct view on render', function () {
 $model = $this->getTestBlogModel();
    $component = new MediaModal(
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
        modelOrClassName: $model,
        mediaCollection: 'image_collection',
        mediaCollections: null,
        title: 'Render Test',
        frontendTheme: $frontendTheme
    );
    $view = $component->render();
//    dd($view);
    expect($view->name())->toBe('media-library-extensions::components.bootstrap-5.media-modal');
});

it('renders the correct Blade view (plain)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);
    $frontendTheme = 'plain';
    $component = new MediaModal(
        modelOrClassName: $model,
        mediaCollection: 'image_collection',
        mediaCollections: null,
        title: 'Render Test',
        frontendTheme: $frontendTheme
    );
    $view = $component->render();
    expect($view->name())->toBe('media-library-extensions::components.plain.media-modal');

});

it('renders the correct html multiple media-collections (plain)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-modal id="test-media-modal" :model-or-class-name="$model" :media-collections="$collections" title="test" :frontend-theme="$frontendTheme" />',
        [
            'model' => $model,
            'collections' => ['image_collection', 'document_collection', 'video_collection', 'audio_collection', 'youtube_collection'],
            'frontendTheme' => 'plain'
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('renders the correct html multiple media-collections (bootstrap-5)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-modal id="test-media-modal" :model-or-class-name="$model" :media-collections="$collections" title="test" :frontend-theme="$frontendTheme" />',
        [
            'model' => $model,
            'collections' => ['image_collection', 'document_collection', 'video_collection', 'audio_collection', 'youtube_collection'],
            'frontendTheme' => 'bootstrap-5'
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('renders the correct html single media-collection (plain)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-modal id="test-media-modal" :model-or-class-name="$model" :media-collection="$collection" title="test" :frontend-theme="$frontendTheme" />',
        [
            'model' => $model,
            'collection' => 'image_collection',
            'frontendTheme' => 'plain'
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('renders the correct html single media-collection (bootstrap-5)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-modal id="test-media-modal" :model-or-class-name="$model" :media-collection="$collection" title="test" :frontend-theme="$frontendTheme" />',
        [
            'model' => $model,
            'collection' => 'image_collection',
            'frontendTheme' => 'bootstrap-5'
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('sets temporary upload mode when given a class string', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $component = new MediaModal(
        modelOrClassName: $model->getMorphClass(),
        mediaCollection: 'images',
        mediaCollections: null,
        title: 'Temp Upload'
    );

    expect($component->temporaryUpload)->toBeTrue()
        ->and($component->model)->toBeNull()
        ->and($component->modelType)->toBe($model->getMorphClass());
});

it('throws if given class string does not exist', function () {
    $modelOrClassName = 'NonExistent\Model';
    expect(fn () => new MediaModal(
        modelOrClassName: $modelOrClassName,
        mediaCollection: null,
        mediaCollections: null,
        title: 'Invalid'
    ))->toThrow(\InvalidArgumentException::class, __('media-library-extensions::messages.class_does_not_exist', [
        'class_name' => $modelOrClassName
    ]));
});

it('throws if given class string does not implement HasMedia', function () {
    expect(fn () => new MediaModal(
        modelOrClassName: \stdClass::class,
        mediaCollection: null,
        mediaCollections: null,
        title: 'Invalid'
    ))->toThrow(\InvalidArgumentException::class, __('media-library-extensions::messages.class_must_implement', [
        'class_name' => HasMedia::class
    ]));
});
