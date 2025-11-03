<?php

use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaFirstAvailable;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('initializes with a HasMedia model and finds the first medium', function () {
    // Will create a model with 4 images
    $model = $this->getModelWithMedia(['image' => 4]);

    $component = new MediaFirstAvailable(
        id: 'media-first-available',
        modelOrClassName: $model,
        collections: ['image' => 'image_collection', 'video' => 'video_collection'],
        options: []
    );

    expect($component->model)->toBe($model)
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->modelId)->toBe($model->getKey())
        ->and($component->medium)->toBeInstanceOf(Media::class)
        ->and($component->medium->collection_name)->toBe('image_collection');
});

it('throws an exception when given a non existing class string(temporary upload)', function () {
    $this->expectException(Exception::class);
    //    $this->expectExceptionMessage('Temporary uploads Not implemented yet');

    new MediaFirstAvailable('test', 'App\\Models\\Something');
});

it('throws an exception when given an invalid type', function () {
    $this->expectException(InvalidArgumentException::class);
    //    $this->expectExceptionMessage('Temporary uploads Not implemented yet');

    new MediaFirstAvailable('test-id', 1234);
});

it('renders the correct view', function () {
    $model = $this->getModelWithMedia(['image' => 1]);

    $component = new MediaFirstAvailable('test-id', $model, ['image' => 'image_collection']);

    $view = $component->render();

    expect($view->name())->toBe('media-library-extensions::components.media-first-available');
});

it('renders expected HTML for an image medium', function () {
    $model = $this->getModelWithMedia(['image' => 1]);

    $html = Blade::renderComponent(new MediaFirstAvailable(
        'test123',
        $model,
        ['image' => 'image_collection']
    ));
    expect($html)->toContain('class="mle-media-preview-image" ')
        ->toContain('test.jpg');
})->only();

it('renders expected HTML for a video medium', function () {
    $model = $this->getModelWithMedia(['video' => 1]);

    $html = Blade::renderComponent(new MediaFirstAvailable(
        'video123',
        $model,
        ['video' => 'video_collection']
    ));

    expect($html)->toContain(' <video')
        ->toContain('/test.mp4" type="video/mp4">');
});

it('renders expected HTML for an audio medium', function () {
    $model = $this->getModelWithMedia(['audio' => 1]);

    $html = Blade::renderComponent(new MediaFirstAvailable(
        'audio123',
        $model, ['audio' => 'audio_collection']
    ));
    expect($html)->toContain('<div class="mle-audio"')
        ->toContain('test.mp3" type="audio/mpeg"');
});

it('renders expected HTML for a document medium', function () {
    $model = $this->getModelWithMedia(['document' => 1]);

    $html = Blade::renderComponent(new MediaFirstAvailable(
        'doc123',
        $model,
        ['document' => 'document_collection']
    ));
    expect($html)->toContain('<div class="mle-document-preview">')
        ->toContain(' test.pdf');
});
