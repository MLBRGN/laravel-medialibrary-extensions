<?php

use Mlbrgn\MediaLibraryExtensions\View\Components\MediaViewer;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('initializes correctly', function () {
    $model = $this->getModelWithMedia(['image' => 1]);
    $medium = $model->getFirstMedia('image_collection');

    $component = new MediaViewer(
        id: 'viewer-id',
        medium: $medium
    );

    expect($component->mediumType)->toBe('image')
        ->and($component->componentToRender)->toBe('mle-image-responsive')
        ->and($component->getDomId())->toBe('viewer-id-media-viewer');
});

it('resolves different media types correctly', function ($mimeType, $expectedType, $expectedComponent) {
    // Create a fake media object
    $medium = new Media();
    $medium->mime_type = $mimeType;

    $component = new MediaViewer(
        id: 'viewer-id',
        medium: $medium
    );

    expect($component->mediumType)->toBe($expectedType)
        ->and($component->componentToRender)->toBe($expectedComponent);
})->with([
    ['image/jpeg', 'image', 'mle-image-responsive'],
    ['video/mp4', 'video', 'mle-video'],
    ['audio/mpeg', 'audio', 'mle-audio'],
    ['application/pdf', 'document', 'mle-document'],
]);

it('renders correctly', function () {
    $model = $this->getModelWithMedia(['image' => 1]);
    $medium = $model->getFirstMedia('image_collection');

    $html = \Illuminate\Support\Facades\Blade::render(
        '<x-mle-media-viewer id="viewer" :medium="$medium" />',
        ['medium' => $medium]
    );

    expect($html)->toContain('data-mle-image')
        ->and($html)->toContain('id="viewer-media-viewer-image-responsive"');
});
