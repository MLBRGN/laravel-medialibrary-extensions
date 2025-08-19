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

function createMockMedia($youtubeId = 'testid'): Media
{
    $media = Mockery::mock(Media::class);
    $media->shouldReceive('getCustomProperty')
        ->with('youtube-id')
        ->andReturn($youtubeId);
    return $media;
}

function parseYouTubeParams(VideoYouTube $component): array
{
    parse_str($component->youTubeParamsAsString, $params);
    return $params;
}

it('builds default YouTube query string correctly', function () {
    $component = new VideoYouTube(medium: createMockMedia(), preview: true);

    $params = parseYouTubeParams($component);

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
    $component = new VideoYouTube(
        medium: createMockMedia(),
        preview: false,
        youtubeParams: ['autoplay' => 0, 'mute' => 0, 'fs' => 0]
    );

    $params = parseYouTubeParams($component);

    expect($params['autoplay'])->toBe('0')
        ->and($params['mute'])->toBe('0')
        ->and($params['fs'])->toBe('0')
        ->and($params['controls'])->toBe('0'); // default preserved
});

it('sets component properties correctly', function () {
    $media = createMockMedia('my-youtube-id');

    $component = new VideoYouTube(medium: $media, preview: false);

    expect($component->medium)->toBe($media)
        ->and($component->preview)->toBeFalse()
        ->and($component->youtubeId)->toBe('my-youtube-id')
        ->and($component->youTubeParamsAsString)->toBeString();
});

it('returns correct view on render', function () {
    $component = new VideoYouTube(createMockMedia());

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class)
        ->and($view->name())->toBe('media-library-extensions::components.video-youtube');
});
