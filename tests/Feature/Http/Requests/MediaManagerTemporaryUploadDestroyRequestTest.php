<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyTemporaryUploadRequest;

it('passes validation when required fields and one collection are provided', function () {
    $data = [
        'base_id' => 'user123',
        'collections' => ['image' => 'images'],
    ];

    $request = new DestroyTemporaryUploadRequest;

    $validator = Validator::make($data, $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('fails validation when no collections are provided', function () {
    $data = [
        'base_id' => 'user123',
    ];

    $request = new DestroyTemporaryUploadRequest;

    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('collections'))->toBeTrue();
});

it('passes validation when a non-image collection is provided', function () {
    $collections = ['video_collection', 'audio_collection', 'document_collection', 'youtube_collection'];

    foreach ($collections as $collection) {
        $data = [
            'base_id' => 'user123',
            $collection => 'some_collection',
            'collections' => ['image' => 'images'],
        ];

        $request = new DestroyTemporaryUploadRequest;

        $validator = Validator::make($data, $request->rules());

        expect($validator->passes())->toBeTrue();
    }
});
