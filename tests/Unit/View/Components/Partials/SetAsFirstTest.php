<?php

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
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
        modelOrClassName: $model,
        media: $mediaCollection,
        medium: $medium,
        singleMedium: null,
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
        modelOrClassName: $model,
        media: $mediaCollection,
        medium: $medium,
        singleMedium: null,
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

it('renders the set as first form with temporary upload', function () {
    $media = collect([]);

    $temporaryUpload = new TemporaryUpload([
        'id' => 1,
        'uuid' => 'test-uuid',
        'file_name' => 'test.jpg',
        'collection_name' => 'temp-uploads',
        'disk' => 'media',
        'mime_type' => 'image/jpeg',
        'custom_properties' => [],
    ]);

    $component = new SetAsFirstForm(
        id: 'set-as-first-btn',
        modelOrClassName: Blog::class,
        media: $media,
        medium: $temporaryUpload,
        singleMedium: null,
        collections: [
            'image' => 'images',
            'document' => 'documents',
            'youtube' => 'youtube',
            'video' => 'video',
            'audio' => 'audio',
        ],
        options: [
            'frontendTheme' => 'plain',
            'useXhr' => false,
            'showSetAsFirstButton' => true,
        ],
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
});
