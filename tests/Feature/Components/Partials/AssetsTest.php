<?php

use Illuminate\Support\Facades\Blade;

it('renders assets view with expected content, theme plain', function () {
    $theme = 'plain';
    $html = Blade::render('<x-mle-shared-assets include-css="true" include-js="true" theme="'.$theme.'"/>');

    // Component now renders a JSON config script and a module loader
    expect($html)
        ->toContain('class="mlbrgn-medialibrary-config"')
        ->and($html)->toContain('type="application/json"')
        ->and($html)->toContain(config('medialibrary-extensions.asset_path').'/js/core/media-library-loader.js');
});

it('renders assets view with expected content, theme bootstrap-5', function () {
    $theme = 'bootstrap-5';
    $html = Blade::render('<x-mle-shared-assets include-css="true" include-js="true" theme="'.$theme.'"/>');

    expect($html)
        ->toContain('class="mlbrgn-medialibrary-config"')
        ->and($html)->toContain('type="application/json"')
        ->and($html)->toContain(config('medialibrary-extensions.asset_path').'/js/core/media-library-loader.js');
});
