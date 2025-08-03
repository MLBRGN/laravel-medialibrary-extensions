<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;
use Mlbrgn\MediaLibraryExtensions\View\Components\Document;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('renders the correct view with given properties', function () {
    $media = mock(Media::class);

    $component = new Document($media, 'Alternative Text');

    expect($component->medium)->toBe($media);
    expect($component->alt)->toBe('Alternative Text');
    expect($component->render()->name())->toBe('media-library-extensions::components.document');
});

it('uses default alt text if none is provided', function () {
    $component = new Document(null);

    expect($component->alt)->toBe('');
    expect($component->medium)->toBeNull();
});
