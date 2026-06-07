<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;

it('passes validation with valid YouTube URL and required fields', function () {
    $data = [
        'temporary_upload_mode' => 'false',
        'model_type' => 'App\\Models\\Post',
        'model_id' => 1,
        'collections' => [
            'image' => null,
            'document' => null,
            'youtube' => 'youtube_videos',
            'audio' => null,
            'video' => null,
        ],
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'initiator_id' => 'user123',
        'media_manager_id' => 'manager123',
        'multiple' => 'true',
    ];

    $request = new StoreYouTubeVideoRequest;

    $validator = Validator::make($data, $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('fails validation with invalid YouTube URL', function () {
    $data = [
        'temporary_upload_mode' => 'false',
        'model_type' => 'App\\Models\\Post',
        'model_id' => 1,
        'collections' => [
            'image' => null,
            'document' => null,
            'youtube' => 'youtube_videos',
            'audio' => null,
            'video' => null,
        ],
        'youtube_url' => 'https://invalid.com/watch?v=abc123',
        'initiator_id' => 'user123',
        'media_manager_id' => 'manager123',
        'multiple' => 'true',
    ];

    $request = new StoreYouTubeVideoRequest;

    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->get('youtube_url')[0])->toBe(__('medialibrary-extensions::messages.invalid_youtube_url'));
});

it('fails when required fields are missing', function () {
    $request = new StoreYouTubeVideoRequest;

    $data = []; // empty input

    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('temporary_upload_mode'))->toBeTrue();
    expect($validator->errors()->has('model_type'))->toBeTrue();
    expect($validator->errors()->has('collections'))->toBeTrue();
    expect($validator->errors()->has('initiator_id'))->toBeTrue();
    expect($validator->errors()->has('media_manager_id'))->toBeTrue();
    expect($validator->errors()->has('multiple'))->toBeTrue();
});
