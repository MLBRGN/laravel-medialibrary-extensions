<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryMediumAsFirstRequest;

beforeEach(function () {
    $this->request = new SetTemporaryMediumAsFirstRequest;
});

it('authorizes all requests', function () {
    expect($this->request->authorize())->toBeTrue();
});

it('passes validation with required fields and at least one collection', function () {
    $data = [
        'target_media_collection' => 'images',
        'medium_id' => '123',
        'initiator_id' => 'user123',
        'media_manager_id' => 'manager456',
        'image_collection' => 'images',
    ];

    $validator = Validator::make($data, $this->request->rules());

    expect($validator->passes())->toBeTrue();
});

it('fails validation when no collections are provided', function () {
    $data = [
        'target_media_collection' => 'images',
        'medium_id' => '123',
        'initiator_id' => 'user123',
        'media_manager_id' => 'manager456',
    ];

    $validator = Validator::make($data, $this->request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('image_collection'))->toBeTrue();
    expect($validator->errors()->has('video_collection'))->toBeTrue();
    expect($validator->errors()->has('audio_collection'))->toBeTrue();
    expect($validator->errors()->has('document_collection'))->toBeTrue();
    expect($validator->errors()->has('youtube_collection'))->toBeTrue();
});

it('fails validation when required fields are missing', function () {
    $data = [];

    $validator = Validator::make($data, $this->request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('target_media_collection'))->toBeTrue();
    expect($validator->errors()->has('medium_id'))->toBeTrue();
    expect($validator->errors()->has('initiator_id'))->toBeTrue();
    expect($validator->errors()->has('media_manager_id'))->toBeTrue();
});
