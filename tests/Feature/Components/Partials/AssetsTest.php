<?php

use Illuminate\Support\Facades\Blade;

it('renders assets view with expected content, theme plain', function () {
    $theme = 'plain';
    $html = Blade::render('<x-mle-shared-assets include-css="true" include-js="true" theme="'.$theme.'"/>');

    expect($html)
        ->toContain('<link rel="stylesheet" href="')
        ->and($html)->toContain('mlbrgn-css-')
        ->and($html)->toContain(config('medialibrary-extensions.asset_path').'/js/app')
        ->and($html)->toContain('window.mediaLibraryTranslations = {')
        ->and($html)->toContain('media-library-extensions');
})->todo();

it('renders assets view with expected content, theme bootstrap-5', function () {
    $theme = 'bootstrap-5';
    $html = Blade::render('<x-mle-shared-assets include-css="true" include-js="true" theme="'.$theme.'"/>');

    expect($html)
        ->toContain('<link rel="stylesheet" href="')
        ->and($html)->toContain('mlbrgn-css-')
        ->and($html)->toContain(config('medialibrary-extensions.asset_path').'/js/app')
        ->and($html)->toContain('window.mediaLibraryTranslations = {')
        ->and($html)->toContain('media-library-extensions');
})->todo();
