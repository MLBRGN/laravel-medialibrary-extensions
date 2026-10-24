<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::set('medialibrary-extensions.frontend_theme', 'bootstrap-6');
});

it('renders bootstrap-6 carousel and matches snapshot', function () {
    $model = $this->getModelWithMedia(['image' => 1]);
    $collections = ['images'];

    $html = Blade::render('<x-mle-media-carousel
                    :model-reference="$modelReference"
                    id="media-carousel-bs6"
                    :collections="$collections"
                />', [
        'modelReference' => $model,
        'collections' => $collections,
    ]);

    // Normalize volatile attributes
    $html = preg_replace('/value="[A-Z0-9]{26}"/', 'value="NORMALIZED"', $html);
    $html = preg_replace('/"clientToken":"[A-Z0-9]{26}"/', '"clientToken":"NORMALIZED"', $html);
    $html = preg_replace('/"instanceId":"[A-Z0-9]{26}"/', '"instanceId":"NORMALIZED"', $html);

    // Check for some Bootstrap 6 specific markers
    expect($html)->toContain('carousel-overlay')
        ->and($html)->toContain('dialog')
        ->and($html)->toMatchSnapshot();
});

it('renders bootstrap-6 media manager and matches snapshot', function () {
    $model = $this->getModelWithMedia(['image' => 1]);
    $collections = ['images'];

    $html = Blade::render('<x-mle-media-manager
                    :model-reference="$modelReference"
                    id="media-manager-bs6"
                    :collections="$collections"
                    theme="bootstrap-6"
                />', [
        'modelReference' => $model,
        'collections' => $collections,
    ]);

    // Normalize volatile attributes
    $html = preg_replace('/value="[A-Z0-9]{26}"/', 'value="NORMALIZED"', $html);
    $html = preg_replace('/"clientToken":"[A-Z0-9]{26}"/', '"clientToken":"NORMALIZED"', $html);
    $html = preg_replace('/"instanceId":"[A-Z0-9]{26}"/', '"instanceId":"NORMALIZED"', $html);

    expect($html)->toContain('btn-solid theme-primary')
        ->and($html)->toMatchSnapshot();
});
