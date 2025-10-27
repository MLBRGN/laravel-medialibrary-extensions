<?php

use Illuminate\Support\Facades\Request;
use Mlbrgn\MediaLibraryExtensions\Rules\AtLeastOneCollection;

beforeEach(function () {
    $this->rule = new AtLeastOneCollection;
});

it('fails when no collections are provided', function () {
    // TODO
    Request::swap(new \Illuminate\Http\Request([
        'image_collection' => null,
        'document_collection' => null,
        'video_collection' => null,
        'audio_collection' => null,
        'youtube_collection' => null,
    ]));

    $failed = null;

    $this->rule->validate('collections', [], function ($message) use (&$failed) {
        $failed = $message;
    });

    expect($failed)->toBe('At least one collection is required.');
});

it('passes when at least one collection is present', function () {
    // TODO
    $testCases = [
        ['image_collection' => ['file1']],
        ['document_collection' => ['file1']],
        ['video_collection' => ['file1']],
        ['audio_collection' => ['file1']],
        ['youtube_collection' => ['file1']],
        [
            'image_collection' => ['file1'],
            'document_collection' => ['file2'],
        ],
    ];

    foreach ($testCases as $case) {
        Request::swap(new \Illuminate\Http\Request($case));

        $failed = false;

        $this->rule->validate('collections', [], function ($message) use (&$failed) {
            $failed = true;
        });

        expect($failed)->toBeFalse();
    }
});
