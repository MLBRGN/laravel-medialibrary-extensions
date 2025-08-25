<?php

use Mlbrgn\MediaLibraryExtensions\Rules\YouTubeUrl;

it('passes for valid YouTube URLs', function () {
    $rule = new YouTubeUrl();

    $validUrls = [
        'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'http://youtube.com/watch?v=dQw4w9WgXcQ',
        'https://youtu.be/dQw4w9WgXcQ',
        'http://www.youtu.be/dQw4w9WgXcQ',
    ];

    foreach ($validUrls as $url) {
        $failed = false;
        $rule->validate('youtube_url', $url, function ($message) use (&$failed) {
            $failed = true;
        });

        expect($failed)->toBeFalse();
    }
});

it('fails for invalid YouTube URLs', function () {
    $rule = new YouTubeUrl();

    $invalidUrls = [
        'https://vimeo.com/123456',
        'http://example.com/video',
        'ftp://youtube.com/watch?v=dQw4w9WgXcQ',
        'youtube.com/watch?v=dQw4w9WgXcQ', // missing scheme
        '',
        null,
    ];

    foreach ($invalidUrls as $url) {
        $failed = null;
        $rule->validate('youtube_url', $url, function ($message) use (&$failed) {
            $failed = $message;
        });

        expect($failed)->toBe(__('media-library-extensions::messages.invalid_youtube_url'));
    }
});
