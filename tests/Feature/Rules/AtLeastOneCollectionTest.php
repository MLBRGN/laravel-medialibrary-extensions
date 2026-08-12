<?php

use Illuminate\Support\Facades\Request;
use Mlbrgn\MediaLibraryExtensions\Rules\AtLeastOneCollection;
use Illuminate\Support\Facades\Validator;

beforeEach(function () {
    $this->rule = new AtLeastOneCollection;
});

it('fails when no collections are provided', function () {
    Request::swap(new Illuminate\Http\Request([
        'image_collection' => null,
        'document_collection' => null,
        'video_collection' => null,
        'audio_collection' => null,
        'youtube_collection' => null,
    ]));

    $validator = Validator::make(
        ['collections' => []],
        ['collections' => [new AtLeastOneCollection]]
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('collections'))
        ->toBe(__('medialibrary-extensions::messages.at_least_one_collection_is_required'));
});

it('passes when at least one collection is present', function () {
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
        Request::swap(new Illuminate\Http\Request($case));

        $validator = Validator::make(
            ['collections' => []],
            ['collections' => [new AtLeastOneCollection]]
        );

        expect($validator->passes())->toBeTrue();
    }
});
