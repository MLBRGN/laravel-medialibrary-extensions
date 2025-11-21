<?php

use Illuminate\Support\Facades\Blade;

it('renders assets view with expected content, theme plain', function () {
    $frontendTheme = 'plain';
    $html = Blade::render('<x-mle-shared-assets include-css="true" include-js="true" frontend-theme="'.$frontendTheme.'"/>');

    expect($html)
        ->toContain('<link rel="stylesheet" href="')
        ->and($html)->toContain('mlbrgn-css-')
        ->and($html)->toContain('vendor/mlbrgn/media-library-extensions/js/app')
        ->and($html)->toContain('window.mediaLibraryTranslations = {')
        ->and($html)->toContain('media-library-extensions');
});

it('renders assets view with expected content, theme bootstrap-5', function () {
    $frontendTheme = 'bootstrap-5';
    $html = Blade::render('<x-mle-shared-assets include-css="true" include-js="true" frontend-theme="'.$frontendTheme.'"/>');

    expect($html)
        ->toContain('<link rel="stylesheet" href="')
        ->and($html)->toContain('mlbrgn-css-')
        ->and($html)->toContain('vendor/mlbrgn/media-library-extensions/js/app')
        ->and($html)->toContain('window.mediaLibraryTranslations = {')
        ->and($html)->toContain('media-library-extensions');
});
