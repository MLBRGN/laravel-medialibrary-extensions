<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;

it('passes validation with valid YouTube URL and required fields', function () {
    $uploadFieldName = config('media-library-extensions.upload_field_name_youtube');

    $data = [
        'temporary_upload_mode' => 'false',
        'model_type' => 'App\\Models\\Post',
        'model_id' => 1,
        'youtube_collection' => 'youtube_videos',
        'image_collection' => null,
        'document_collection' => null,
        'video_collection' => null,
        'audio_collection' => null,
        $uploadFieldName => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'initiator_id' => 'user123',
        'media_manager_id' => 'manager123',
        'multiple' => 'true',
    ];

    $request = new StoreYouTubeVideoRequest;

    $validator = Validator::make($data, $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('fails validation with invalid YouTube URL', function () {
    $uploadFieldName = config('media-library-extensions.upload_field_name_youtube');

    $data = [
        'temporary_upload_mode' => 'false',
        'model_type' => 'App\\Models\\Post',
        'model_id' => 1,
        'youtube_collection' => 'youtube_videos',
        $uploadFieldName => 'https://invalid.com/watch?v=abc123',
        'initiator_id' => 'user123',
        'media_manager_id' => 'manager123',
        'multiple' => 'true',
    ];

    $request = new StoreYouTubeVideoRequest;

    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->get($uploadFieldName)[0])->toBe(__('media-library-extensions::messages.invalid_youtube_url'));
});

it('fails when required fields are missing', function () {
    $request = new StoreYouTubeVideoRequest;

    $data = []; // empty input

    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('temporary_upload_mode'))->toBeTrue();
    expect($validator->errors()->has('model_type'))->toBeTrue();
    expect($validator->errors()->has('youtube_collection'))->toBeTrue();
    expect($validator->errors()->has('initiator_id'))->toBeTrue();
    expect($validator->errors()->has('media_manager_id'))->toBeTrue();
    expect($validator->errors()->has('multiple'))->toBeTrue();
});
