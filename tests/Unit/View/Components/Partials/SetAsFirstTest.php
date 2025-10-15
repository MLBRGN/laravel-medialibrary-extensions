<?php

use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\SetAsFirstForm;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('renders the set-as-first-form', function () {
    $model = $this->getModelWithMedia(['image' => 2]);

    $mediaCollection = $model->getMedia('image_collection');
    expect($mediaCollection)->toHaveCount(2);

    $medium = $mediaCollection->first();
    expect($medium)->toBeInstanceOf(Media::class);

    $component = new SetAsFirstForm(
        id: 'set-first-btn',
        media: $mediaCollection,
        medium: $medium,
        modelOrClassName: $model,
        options: [],
        frontendTheme: 'plain',
        useXhr: false,
        collections: ['image' => 'images', 'audio' => 'audio', 'video' => 'video', 'document' => 'docs', 'youtube' => 'youtube'],
        showSetAsFirstButton: true,
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
    expect($component->useXhr)->toBeFalse();
});

it('falls back to config use_xhr when useXhr is null', function () {
    $model = $this->getModelWithMedia(['image' => 3]);

    config()->set('media-library-extensions.use_xhr', true);

    $mediaCollection = $model->getMedia('image_collection');
    expect($mediaCollection)->toHaveCount(3);

    $medium = $mediaCollection->first();
    expect($medium)->toBeInstanceOf(Media::class);

    $component = new SetAsFirstForm(
        id: 'set-first-btn',
        media: $mediaCollection,
        medium: $medium,
        modelOrClassName: $model,
        options: [],
        frontendTheme: 'plain',
        useXhr: null,
        collections: ['video' => 'video', 'audio' => 'audio'],
        showSetAsFirstButton: false,
    );

    $component->render();

    expect($component->useXhr)->toBeTrue();
});
