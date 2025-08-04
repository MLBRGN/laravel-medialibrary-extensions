<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Illuminate\Support\Facades\Config;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\VideoYouTube;
use Mockery;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

beforeEach(function () {
    Config::set('media-library-extensions.default_youtube_params', [
        'autoplay' => 1,
        'mute' => 1,
        'loop' => 0,
        'controls' => 0,
        'modestbranding' => 1,
        'playsinline' => 1,
        'rel' => 0,
        'enablejsapi' => 1,
        'cc_load_policy' => 1,
        'cc_lang_pref' => 'en',
        'iv_load_policy' => 3,
        'hl' => 'en',
        'fs' => 1,
    ]);
});

it('builds default YouTube query string correctly', function () {
    $media = Mockery::mock(Media::class);

    $component = new VideoYouTube(
        medium: $media,
        preview: true,
        youtubeId: 'abc123'
    );

    parse_str($component->youTubeParamsAsString, $params);

    expect($params)->toMatchArray([
        'autoplay' => '1',
        'mute' => '1',
        'loop' => '0',
        'controls' => '0',
        'modestbranding' => '1',
        'playsinline' => '1',
        'rel' => '0',
        'enablejsapi' => '1',
        'cc_load_policy' => '1',
        'cc_lang_pref' => 'en',
        'iv_load_policy' => '3',
        'hl' => 'en',
        'fs' => '1',
    ]);
});

it('overrides default params with custom ones', function () {
    $media = Mockery::mock(Media::class);

    $component = new VideoYouTube(
        medium: $media,
        preview: false,
        youtubeId: 'xyz456',
        youtubeParams: ['autoplay' => 0, 'mute' => 0, 'fs' => 0]
    );

    parse_str($component->youTubeParamsAsString, $params);

    expect($params['autoplay'])->toBe('0')
        ->and($params['mute'])->toBe('0')
        ->and($params['fs'])->toBe('0')
        ->and($params['controls'])->toBe('0'); // from default
});

it('sets component properties correctly', function () {
    $media = Mockery::mock(Media::class);

    $component = new VideoYouTube(
        medium: $media,
        preview: false,
        youtubeId: 'testid'
    );

    expect($component->medium)->toBe($media)
        ->and($component->preview)->toBeFalse()
        ->and($component->youtubeId)->toBe('testid')
        ->and($component->youTubeParamsAsString)->toBeString();
});

it('returns correct view on render', function () {
    $media = Mockery::mock(Media::class);

    $component = new VideoYouTube($media);

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class)
        ->and($view->name())->toBe('media-library-extensions::components.video-youtube');
});
