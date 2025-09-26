<?php


use Illuminate\Support\Collection;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\SetAsFirstForm;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;

it('renders the set-as-first-form partial view', function () {
    // Create dummy media collection
    $mediaCollection = Collection::make([
        new Media(['id' => 1, 'collection_name' => 'images']),
        new Media(['id' => 2, 'collection_name' => 'images']),
    ]);

    $medium = new Media([
        'id' => 1,
        'collection_name' => 'images',
        'disk' => 'media',
        'file_name' => 'test.jpg',
        'mime_type' => 'image/jpeg',
        'custom_properties' => [],
    ]);

    // Dummy model implementing HasMedia (can be mocked)
    $model = $this->createMock(HasMedia::class);

    $component = new SetAsFirstForm(
        media: $mediaCollection,
        medium: $medium,
        id: 'set-first-btn',
        frontendTheme: 'plain',
        useXhr: false,
        imageCollection: 'images',
        documentCollection: 'docs',
        youtubeCollection: 'youtube',
        videoCollection:  'video',
        audioCollection: 'audio',
        showSetAsFirstButton: true,
        model: $model,
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
    expect($component->useXhr)->toBeFalse();
});

it('falls back to config use_xhr when useXhr is null', function () {
    config()->set('media-library-extensions.use_xhr', true);

    $mediaCollection = Collection::make([]);
    $medium = new Media(['id' => 1, 'collection_name' => 'images']);
    $model = $this->createMock(HasMedia::class);

    $component = new SetAsFirstForm(
        media: $mediaCollection,
        medium: $medium,
        id: 'set-first-btn',
        frontendTheme: 'plain',
        useXhr: null,
        imageCollection: '',
        documentCollection: '',
        youtubeCollection: '',
        videoCollection:  'video',
        audioCollection: 'audio',
        showSetAsFirstButton: false,
        model: $model,
    );

    $component->render();

    expect($component->useXhr)->toBeTrue();
});
