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
        modelOrClassName: $model,
        medium: $medium,
        collections: ['image' => 'images', 'audio' => 'audio', 'video' => 'video', 'document' => 'docs', 'youtube' => 'youtube'],
        options: [
            'frontendTheme' => 'plain',
            'useXhr' => false,
            'showSetAsFirstButton' => true,
        ],
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
    expect($component->getConfig('useXhr'))->toBeFalse();

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
        modelOrClassName: $model,
        medium: $medium,
        collections: ['video' => 'video', 'audio' => 'audio'],
        options: [
            'frontendTheme' => 'plain',
            'useXhr' => true,
            'showSetAsFirstButton' => true,
        ],
    );

    $component->render();

    expect($component->getConfig('useXhr'))->toBeTrue();
});
