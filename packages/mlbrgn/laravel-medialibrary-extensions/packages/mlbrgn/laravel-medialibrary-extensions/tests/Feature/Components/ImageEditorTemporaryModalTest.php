<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

it('renders temporary image editor modal with medium path and base id', function () {
    $model = $this->getTestBlogModel();

    $temp = TemporaryUpload::factory()->create([
        'collection_name' => 'images',
        'mime_type' => 'image/jpeg',
        'file_name' => 'photo.jpg',
    ]);

    $baseId = 'iem-'.Str::ulid();

    $html = Blade::render(
        <<<'BLADE'
        <x-mle-image-editor-modal
            :id="$id"
            :model-or-class-name="$model"
            :medium="$medium"
            :single-media="null"
            :collections="['image' => 'images']"
            :options="['frontendTheme' => 'plain']"
            :disabled="false"
            :data-source="'default'"
            title="Edit"
        />
        BLADE,
        [
            'id' => $baseId,
            'model' => $model->getMorphClass(),
            'medium' => $temp,
        ]
    );

    expect($html)->toContain('data-mle-image-editor-modal');
    expect($html)->toContain('data-mle-medium-path=');
    expect($html)->toContain('data-base-id="'.$baseId.'"');
});
