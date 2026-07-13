<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('exposes total media count for a single manager', function () {
    $this->withoutVite();

    // Create a model with 1 image in the target collection via helper
    $model = $this->getModelWithMedia(['image' => 1]);

    // Render a single media manager for the images collection
    $html = Blade::render(
        '<x-mle-media-manager
            :id="\'mgr\'"
            :model-or-class-name="$modelOrClassName"
            :collections="[\'image\' => \'image_collection\']"
            :multiple="false"
        />',
        [
            'modelOrClassName' => $model,
        ]
    );

    // Assert the config JSON is present with expected counters
    expect($html)
        ->toContain('"totalMediaCount":1')
        ->toContain('"maxMediaCount":1')
        ->toContain('"isAtMax":true')
        ->toContain('"multiple":false');

    // Assert visible counter text in the upload form section
    expect($html)
        ->toContain(__('medialibrary-extensions::messages.media_counts', [
            'current' => 1,
            'total' => 1,
        ]));
});
