<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerDestroyRequest;

it('passes validation when required fields and one collection are provided', function () {
    $data = [
        'initiator_id' => 'user123',
        'media_manager_id' => 'manager456',
        'image_collection' => 'images',
    ];

    $request = new MediaManagerDestroyRequest();

    $validator = Validator::make($data, $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('fails validation when no collections are provided', function () {
    $data = [
        'initiator_id' => 'user123',
        'media_manager_id' => 'manager456',
    ];

    $request = new MediaManagerDestroyRequest();

    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('image_collection'))->toBeTrue();
    expect($validator->errors()->has('video_collection'))->toBeTrue();
    expect($validator->errors()->has('audio_collection'))->toBeTrue();
    expect($validator->errors()->has('document_collection'))->toBeTrue();
    expect($validator->errors()->has('youtube_collection'))->toBeTrue();
});

it('passes validation when a non-image collection is provided', function () {
    $collections = ['video_collection', 'audio_collection', 'document_collection', 'youtube_collection'];

    foreach ($collections as $collection) {
        $data = [
            'initiator_id' => 'user123',
            'media_manager_id' => 'manager456',
            $collection => 'some_collection',
        ];

        $request = new MediaManagerDestroyRequest();

        $validator = Validator::make($data, $request->rules());

        expect($validator->passes())->toBeTrue();
    }
});
