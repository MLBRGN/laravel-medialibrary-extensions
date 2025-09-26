<?php

use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewInstance;
use Mlbrgn\MediaLibraryExtensions\View\Components\ImageResponsive;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('renders image responsive component', function () {
    $media = Mockery::mock(Media::class);

    $media->shouldReceive('getCustomProperty')
        ->with('generated_conversions', [])
        ->andReturn(['thumb' => true, 'web' => false]);

    $media->shouldReceive('hasGeneratedConversion')
        ->with('thumb')
        ->andReturn(true);

    $media->shouldReceive('getUrl')
        ->with('thumb')
        ->andReturn('/media/thumb.jpg');

    $media->shouldReceive('getSrcset')
        ->with('thumb')
        ->andReturn('/media/thumb-1x.jpg 1x, /media/thumb-2x.jpg 2x');

    $html = Blade::render('<x-mle-image-responsive
        :medium="$medium"
        conversion="thumb"
        :conversions="[\'thumb\', \'web\']"
        sizes="50vw"
        :lazy="false"
        alt="Sample image"
    />', [
        'medium' => $media,
    ]);

    expect($html)
        ->toContain('src="/media/thumb.jpg"')
        ->toContain('srcset="/media/thumb-1x.jpg 1x, /media/thumb-2x.jpg 2x"')
        ->toContain('alt="Sample image"')
        ->toContain('sizes="50vw"');

    expect($html)->toMatchSnapshot();
});

it('returns empty conversion if no media is provided', function () {
    $component = new ImageResponsive(null);

    expect($component->hasGeneratedConversion())->toBeFalse()
        ->and($component->getUseConversion())->toBe('');
});

it('uses explicitly provided valid conversion', function () {
    $media = mock(Media::class);

    $media->shouldReceive('getCustomProperty')
        ->with('generated_conversions', [])
        ->andReturn(['thumb' => true, 'web' => false]);

    $media->shouldReceive('hasGeneratedConversion')
        ->with('thumb')
        ->andReturn(true);

    $component = new ImageResponsive($media, conversion: 'thumb');

    expect($component->getUseConversion())->toBe('thumb')
        ->and($component->hasGeneratedConversion())->toBeTrue();
});

it('falls back to first valid conversion in list', function () {
    $media = mock(Media::class);
    $media->shouldReceive('getCustomProperty')
        ->with('generated_conversions', [])
        ->andReturn(['thumb' => true, 'web' => false]);
    $media->shouldReceive('hasGeneratedConversion')->with('thumb')->andReturn(true);
    $media->shouldReceive('hasGeneratedConversion')->with('web')->andReturn(false);

    $component = new ImageResponsive($media, conversions: ['thumb', 'web']);

    expect($component->getUseConversion())->toBe('thumb')
        ->and($component->hasGeneratedConversion())->toBeTrue();
});

it('returns empty conversion if none of the conversions are valid', function () {
    $media = mock(Media::class);
    $media->shouldReceive('getCustomProperty')
        ->with('generated_conversions', [])
        ->andReturn(['thumb' => true, 'web' => false]);
    $media->shouldReceive('hasGeneratedConversion')->andReturn(false);

    $component = new ImageResponsive($media, conversion: 'foo', conversions: ['bar', 'baz']);

    expect($component->getUseConversion())->toBe('')
        ->and($component->hasGeneratedConversion())->toBeFalse();
});

it('renders the correct view with expected data when a valid conversion is used', function () {
    $media = mock(Media::class);
    $media->shouldReceive('getCustomProperty')
        ->with('generated_conversions', [])
        ->andReturn(['thumb' => true, 'web' => false]);
    $media->shouldReceive('hasGeneratedConversion')->with('thumb')->andReturn(true);
    $media->shouldReceive('getUrl')->with('thumb')->andReturn('http://example.com/thumb.jpg');
    $media->shouldReceive('getSrcset')->with('thumb')->andReturn('http://example.com/thumb@2x.jpg 2x');

    // Create a mock view to be returned
    $mockView = Mockery::mock(ViewInstance::class);

    // Expect the view to be made with proper data and return the mocked View
    View::shouldReceive('make')
        ->once()
        ->with(
            'media-library-extensions::components.image-responsive',
            Mockery::on(function ($data) {
                return $data['hasGeneratedConversion'] === true
                    && $data['useConversion'] === 'thumb'
                    && $data['url'] === 'http://example.com/thumb.jpg'
                    && $data['srcset'] === 'http://example.com/thumb@2x.jpg 2x';
            }),
            []
        )
        ->andReturn($mockView); // Must return a View instance

    $component = new \Mlbrgn\MediaLibraryExtensions\View\Components\ImageResponsive($media, conversion: 'thumb');
    $result = $component->render();

    expect($result)->toBe($mockView); // optionally verify it's the view
});

it('falls back to original URL on exception', function () {
    $media = mock(\Spatie\MediaLibrary\MediaCollections\Models\Media::class);
    $media->shouldReceive('getCustomProperty')
        ->with('generated_conversions', [])
        ->andReturn(['thumb' => true, 'web' => false]);
    $media->shouldReceive('hasGeneratedConversion')->with('thumb')->andReturn(true);
    $media->shouldReceive('getUrl')->with('thumb')->andThrow(new \Exception('fail'));
    $media->shouldReceive('getUrl')->withNoArgs()->andReturn('http://example.com/original.jpg');
    $media->shouldReceive('getSrcset')->with('thumb')->andReturnUsing(function () {
        throw new \Exception('fail');
    });

    $mockView = Mockery::mock(ViewInstance::class);

    View::shouldReceive('make')
        ->once()
        ->with(
            'media-library-extensions::components.image-responsive',
            Mockery::on(function ($data) {
                return $data['hasGeneratedConversion'] === true
                    && $data['useConversion'] === 'thumb'
                    && $data['url'] === 'http://example.com/original.jpg'
                    && $data['srcset'] === ''; // fallback logic sets empty string
            }),
            []
        )
        ->andReturn($mockView);

    $component = new \Mlbrgn\MediaLibraryExtensions\View\Components\ImageResponsive($media, conversion: 'thumb');
    $result = $component->render();

    expect($result)->toBe($mockView);
});
