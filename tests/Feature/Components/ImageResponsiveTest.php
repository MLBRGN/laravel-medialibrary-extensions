<?php

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\ImageResponsive;

it('renders with a media object', function () {

    $medium = $this->getMedium();

    $component = new ImageResponsive(
        medium: $medium,
        conversion: 'thumb',
        sizes: '100vw',
        lazy: true,
        alt: 'Test image'
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
    expect($component->hasGeneratedConversion())->toBeTrue();
    expect($component->getUseConversion())->toBe('thumb');
});

it('falls back to alternative conversion when primary is not available', function () {
    $medium = $this->getMedium();
    $medium->generated_conversions = [
        'primary' => false,
        'fallback' => true,
    ];

    $component = new ImageResponsive(
        medium: $medium,
        conversion: 'primary',
        conversions: ['fallback'],
        sizes: '100vw',
        lazy: true,
        alt: 'Test image'
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
    expect($component->hasGeneratedConversion())->toBeTrue();
    expect($component->getUseConversion())->toBe('fallback');
});

it('uses original image when no conversions are available', function () {
    $medium = $this->getMedium();
    $medium->generated_conversions = [
        'unavailable' => false,
        'also-unavailable' => false,
    ];

    $component = new ImageResponsive(
        medium: $medium,
        conversion: 'unavailable',
        conversions: ['also-unavailable'],
        sizes: '100vw'
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
    expect($component->hasGeneratedConversion())->toBeFalse();
    expect($component->getUseConversion())->toBe('');
});

it('handles null media gracefully', function () {

    $component = new ImageResponsive(
        medium: null,
        sizes: '100vw'
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
    expect($component->hasGeneratedConversion())->toBeFalse();
    expect($component->getUseConversion())->toBe('');
});

it('handles exceptions when getting media URL', function () {
    $medium = $this->getMedium();
    $medium->generated_conversions = ['thumb' => true];

    $component = new ImageResponsive(
        medium: $medium,
        conversion: 'thumb',
        sizes: '100vw'
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
    expect($component->hasGeneratedConversion())->toBeTrue();
});
