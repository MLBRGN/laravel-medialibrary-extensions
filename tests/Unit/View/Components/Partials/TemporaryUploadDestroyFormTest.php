<?php

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\TemporaryUploadDestroyForm;

it('renders the temporary-upload-destroy-form partial view', function () {
    $temporaryUpload = new TemporaryUpload([
        'id' => 1,
        'uuid' => 'test-uuid',
        'file_name' => 'test.jpg',
        'collection_name' => 'temp-uploads',
        'disk' => 'media',
        'mime_type' => 'image/jpeg',
        'custom_properties' => [],
    ]);

    $component = new TemporaryUploadDestroyForm(
        medium: $temporaryUpload,
        id: 'delete-temp-upload-btn',
        frontendTheme: 'plain',
        useXhr: true,
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
});
