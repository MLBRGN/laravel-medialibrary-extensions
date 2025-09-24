<?php

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
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
        media: $media,
        medium: $temporaryUpload,
        id: 'set-as-first-btn',
        frontendTheme: 'plain',
        useXhr: null,
        imageCollection: 'images',
        documentCollection: 'documents',
        youtubeCollection: 'youtube',
        videoCollection:  'videos',
        audioCollection: 'audios',
        showSetAsFirstButton: true,
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
});
