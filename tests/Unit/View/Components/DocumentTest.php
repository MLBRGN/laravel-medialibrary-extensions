<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\View\Components\Document;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

test('document component renders', function () {
    Storage::fake('media');

    $medium = $this->getMediaModelWithMedia(['document' => 1]);

    $html = Blade::render('<x-mle-document
                    :medium="$medium"
                    alt="alternative text"
                />', [
        'medium' => $medium,
    ]);

    expect($html)
        ->toContain('class="mle-document"')
        ->toContain('class="mle-document-preview"')
        ->toContain('PDF document');

    // update snapshots with --update-snapshots when running pest
    expect($html)->toMatchSnapshot();
});

test('document component renders unknown file type', function () {
    Storage::fake('media');

    $medium = new Media([
        'id' => 1,
        'collection_name' => 'blog-images',
        'disk' => 'media',
        'file_name' => 'test.jpg',
        'mime_type' => 'image/jpeg',
        'custom_properties' => [],
    ]);

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
        ->toContain(__('media-library-extensions::messages.unknown_file_mimetype'));
});

it('renders the correct view with given properties', function () {
    // Use a real Media instance instead of a pure mock
    $media = new Media([
        'mime_type' => 'application/pdf',
    ]);
    $media->exists = true;

    $component = new Document($media,  false, 'Alternative Text');

    expect($component->medium)->toBe($media);
    expect($component->alt)->toBe('Alternative Text');
    expect($component->render()->name())->toBe('media-library-extensions::components.document');
});

it('uses default alt text if none is provided', function () {
    // Use a real Media instance or TemporaryUpload with null mime_type
    $media = new Media([
        'mime_type' => null,
    ]);
    $media->exists = false;

    $component = new Document($media);

    expect($component->alt)->toBe('');
    expect($component->medium)->toBe($media);
});

//
//test('document component renders', function () {
//    Storage::fake('media');
//
//    $medium = new Media([
//        'id' => 1,
//        'collection_name' => 'blog-documents',
//        'disk' => 'media',
//        'file_name' => 'test.pdf',
//        'mime_type' => 'application/pdf',
//        'custom_properties' => [],
//    ]);
//
//    // Make sure to set model-related properties that Blade/view logic may expect
//    $medium->exists = true;
//
//    $html = Blade::render('<x-mle-document
//                    :medium="$medium"
//                    alt="alternative text"
//                />', [
//        'medium' => $medium,
//    ]);
//
//    expect($html)
//        ->toContain('class="mle-document"')
//        ->toContain('class="mle-document-preview"')
//        ->toContain('PDF document');
//
//});
//
//test('document component renders unknown file type', function () {
//    Storage::fake('media');
//
//    $medium = new Media([
//        'id' => 1,
//        'collection_name' => 'blog-images',
//        'disk' => 'media',
//        'file_name' => 'test.jpg',
//        'mime_type' => 'image/jpeg',
//        'custom_properties' => [],
//    ]);
//
//    // Make sure to set model-related properties that Blade/view logic may expect
//    $medium->exists = true;
//
//    $html = Blade::render('<x-mle-document
//                    :medium="$medium"
//                    alt="alternative text"
//                />', [
//        'medium' => $medium,
//    ]);
//
//    expect($html)
//        ->toContain('class="mle-document"')
//        ->toContain('class="mle-document-preview"')
//        ->toContain(__('media-library-extensions::messages.unknown_file_mimetype'));
//
//});
//
//it('renders the correct view with given properties', function () {
//    $media = mock(Media::class);
//
//    $component = new Document($media, 'Alternative Text');
//
//    expect($component->medium)->toBe($media);
//    expect($component->alt)->toBe('Alternative Text');
//    expect($component->render()->name())->toBe('media-library-extensions::components.document');
//});
//
//it('uses default alt text if none is provided', function () {
//    $component = new Document(null);
//
//    expect($component->alt)->toBe('');
//    expect($component->medium)->toBeNull();
//})->todo();
