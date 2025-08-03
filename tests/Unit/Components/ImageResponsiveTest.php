<?php

/**
 * EXAMPLE TEST FILE
 *
 * This file contains example tests for the ImageResponsive component.
 * These tests demonstrate how to use Pest to test components in isolation.
 * You may need to adapt these tests to your specific environment.
 */

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\ImageResponsive;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('renders with a media object', function () {
    // Arrange
    $medium = Mockery::mock(Media::class);
    $medium->shouldReceive('hasGeneratedConversion')->with('thumb')->andReturn(true);
    $medium->shouldReceive('getUrl')->with('thumb')->andReturn('https://example.com/thumb.jpg');
    $medium->shouldReceive('getSrcset')->with('thumb')->andReturn('https://example.com/thumb.jpg 300w, https://example.com/thumb.jpg 600w');

    // Act
    $component = new ImageResponsive(
        medium: $medium,
        conversion: 'thumb',
        sizes: '100vw',
        lazy: true,
        alt: 'Test image'
    );

    $view = $component->render();

    // Assert
    expect($view)->toBeInstanceOf(View::class);
    expect($component->hasGeneratedConversion())->toBeTrue();
    expect($component->getUseConversion())->toBe('thumb');
})->skip();

it('falls back to alternative conversion when primary is not available', function () {
    // Arrange
    $medium = Mockery::mock(Media::class);
    $medium->shouldReceive('hasGeneratedConversion')->with('primary')->andReturn(false);
    $medium->shouldReceive('hasGeneratedConversion')->with('fallback')->andReturn(true);
    $medium->shouldReceive('getUrl')->with('fallback')->andReturn('https://example.com/fallback.jpg');
    $medium->shouldReceive('getSrcset')->with('fallback')->andReturn('https://example.com/fallback.jpg 300w, https://example.com/fallback.jpg 600w');

    // Act
    $component = new ImageResponsive(
        medium: $medium,
        conversion: 'primary',
        conversions: ['fallback'],
        sizes: '100vw',
        lazy: true,
        alt: 'Test image'
    );

    $view = $component->render();

    // Assert
    expect($view)->toBeInstanceOf(View::class);
    expect($component->hasGeneratedConversion())->toBeTrue();
    expect($component->getUseConversion())->toBe('fallback');
})->skip();

it('uses original image when no conversions are available', function () {
    // Arrange
    $medium = Mockery::mock(Media::class);
    $medium->shouldReceive('hasGeneratedConversion')->andReturn(false);
    $medium->shouldReceive('getUrl')->withNoArgs()->andReturn('https://example.com/original.jpg');

    // Act
    $component = new ImageResponsive(
        medium: $medium,
        conversion: 'unavailable',
        conversions: ['also-unavailable'],
        sizes: '100vw'
    );

    $view = $component->render();

    // Assert
    expect($view)->toBeInstanceOf(View::class);
    expect($component->hasGeneratedConversion())->toBeFalse();
    expect($component->getUseConversion())->toBe('');
})->skip();

it('handles null media gracefully', function () {
    // Act
    $component = new ImageResponsive(
        medium: null,
        sizes: '100vw'
    );

    $view = $component->render();

    // Assert
    expect($view)->toBeInstanceOf(View::class);
    expect($component->hasGeneratedConversion())->toBeFalse();
    expect($component->getUseConversion())->toBe('');
})->skip();

it('handles exceptions when getting media URL', function () {
    // Arrange
    $medium = Mockery::mock(Media::class);
    $medium->shouldReceive('hasGeneratedConversion')->with('thumb')->andReturn(true);
    $medium->shouldReceive('getUrl')->with('thumb')->andThrow(new Exception('Error getting URL'));
    $medium->shouldReceive('getUrl')->withNoArgs()->andReturn('https://example.com/original.jpg');

    // Act
    $component = new ImageResponsive(
        medium: $medium,
        conversion: 'thumb',
        sizes: '100vw'
    );

    $view = $component->render();

    // Assert
    expect($view)->toBeInstanceOf(View::class);
    // Even though there's an exception, the component should still render
    expect($component->hasGeneratedConversion())->toBeTrue();
})->skip();
