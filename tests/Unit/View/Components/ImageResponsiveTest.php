<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\ImageResponsive;

it('renders image responsive component', function () {
    $medium = $this->getMedium();

    $html = Blade::render('<x-mle-image-responsive
        :medium="$medium"
        conversion="thumb"
        :conversions="[\'thumb\', \'web\']"
        sizes="50vw"
        :lazy="false"
        alt="Sample image"
    />', [
        'medium' => $medium,
    ]);

    expect($html)->toMatchSnapshot();
});

it('returns empty conversion if no media is provided', function () {
    $component = new ImageResponsive(null);

    expect($component->hasGeneratedConversion())->toBeFalse()
        ->and($component->getUseConversion())->toBe('');
});

it('uses explicitly provided valid conversion', function () {
    $media = $this->getMedia();

    $component = new ImageResponsive($media, conversion: 'thumb');

    expect($component->getUseConversion())->toBe('thumb')
        ->and($component->hasGeneratedConversion())->toBeTrue();
});

it('falls back to first valid conversion in list', function () {
    $media = $this->getMedia();

    $component = new ImageResponsive($media, conversions: ['thumb', 'web']);

    expect($component->getUseConversion())->toBe('thumb')
        ->and($component->hasGeneratedConversion())->toBeTrue();
});

it('returns empty conversion if none of the conversions are valid', function () {
    $media = $this->getMedia();

    // assuming your helper returns something with conversions ['thumb', 'web']
    // so this will intentionally not match
    $component = new ImageResponsive($media, conversion: 'foo', conversions: ['bar', 'baz']);

    expect($component->getUseConversion())->toBe('')
        ->and($component->hasGeneratedConversion())->toBeFalse();
});

it('renders the correct view with expected data when a valid conversion is used', function () {
    $media = $this->getMedia();

    View::shouldReceive('make')
        ->once()
        ->with(
            'media-library-extensions::components.image-responsive',
            Mockery::on(function ($data) use ($media) {
                return $data['hasGeneratedConversion'] === true
                    && $data['useConversion'] === 'thumb'
                    && $data['url'] === $media->getUrl('thumb')
                    && $data['srcset'] === $media->getSrcset('thumb');
            }),
            []
        )
        ->andReturn($mockView = Mockery::mock(\Illuminate\Contracts\View\View::class));

    $component = new ImageResponsive($media, conversion: 'thumb');
    $result = $component->render();

    expect($result)->toBe($mockView);
})->todo();

it('falls back to original URL on exception', function () {
    $media = $this->getMedia();

    // simulate exception
    $media->setCustomProperty('simulate_exception', true);

    View::shouldReceive('make')
        ->once()
        ->with(
            'media-library-extensions::components.image-responsive',
            Mockery::on(function ($data) {
                return $data['hasGeneratedConversion'] === true
                    && $data['useConversion'] === 'thumb'
                    && str_contains($data['url'], 'original')
                    && $data['srcset'] === '';
            }),
            []
        )
        ->andReturn($mockView = Mockery::mock(\Illuminate\Contracts\View\View::class));

    $component = new ImageResponsive($media, conversion: 'thumb');
    $result = $component->render();

    expect($result)->toBe($mockView);
})->todo();
