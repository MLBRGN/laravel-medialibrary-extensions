<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\View\Components\Document;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

test('document component renders', function () {
    Storage::fake('media');

    $medium = new Media([
        'id' => 1,
        'collection_name' => 'blog-images',
        'disk' => 'media',
        'file_name' => 'test.jpg',
        'mime_type' => 'image/jpeg',
        'custom_properties' => [],
    ]);

    // Make sure to set model-related properties that Blade/view logic may expect
    $medium->exists = true;

    $html = Blade::render('<x-mle-document
                    :medium="$medium"
                    alt="alternative text"
                />', [
        'medium' => $medium,
    ]);

    expect($html)
        ->toContain('class="mle-document"')
        ->toContain('class="mle-document-preview"')
        ->toContain($medium->id.'/test.jpg');

});

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
