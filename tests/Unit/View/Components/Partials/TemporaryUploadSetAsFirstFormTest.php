<?php

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\TemporaryUploadSetAsFirstForm;

it('renders the temporary-upload-set-as-first-form partial view', function () {
    $media = collect([
        // Add some dummy media items if needed or leave empty
    ]);

    $temporaryUpload = new TemporaryUpload([
        'id' => 1,
        'uuid' => 'test-uuid',
        'file_name' => 'test.jpg',
        'collection_name' => 'temp-uploads',
        'disk' => 'media',
        'mime_type' => 'image/jpeg',
        'custom_properties' => [],
    ]);

    $component = new TemporaryUploadSetAsFirstForm(
        id: 'set-as-first-btn',
        media: $media,
        medium: $temporaryUpload,
        modelOrClassName: Blog::class,
        options: [
            'frontendTheme' => 'plain',
            'useXhr' => false,
            'showSetAsFirstButton' => true,
        ],
        collections: [
            'image' => 'images',
            'document' => 'documents',
            'youtube' => 'youtube',
            'video' => 'video',
            'audio' => 'audio',
        ],
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
});
