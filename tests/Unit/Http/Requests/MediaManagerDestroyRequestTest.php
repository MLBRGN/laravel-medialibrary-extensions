<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyRequest;

it('passes validation when required fields and one collection are provided', function () {
    $data = [
        'initiator_id' => 'user123',
        'media_manager_id' => 'manager456',
        'collections' => ['image' => 'images'],
    ];

    $request = new DestroyRequest;

    $validator = Validator::make($data, $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('fails validation when no collections are provided', function () {
    $data = [
        'initiator_id' => 'user123',
        'media_manager_id' => 'manager456',
    ];

    $request = new DestroyRequest;

    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('collections'))->toBeTrue();
});

it('passes validation when a non-image collection is provided', function () {
    $collections = ['video_collection', 'audio_collection', 'document_collection', 'youtube_collection'];

    foreach ($collections as $collection) {
        $data = [
            'initiator_id' => 'user123',
            'media_manager_id' => 'manager456',
            $collection => 'some_collection',
            'collections' => ['image' => 'images'],

        ];

        $request = new DestroyRequest;

        $validator = Validator::make($data, $request->rules());

        expect($validator->passes())->toBeTrue();
    }
});
