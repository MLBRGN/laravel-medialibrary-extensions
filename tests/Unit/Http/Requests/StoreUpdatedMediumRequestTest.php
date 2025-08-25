<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreUpdatedMediumRequest;

beforeEach(function () {
    $this->request = new StoreUpdatedMediumRequest();
});

it('authorizes all requests', function () {
    expect($this->request->authorize())->toBeTrue();
});

it('passes validation with all required fields', function () {
    $model = $this->getTestBlogModel();
    $data = [
        'initiator_id' => 'user123',
        'media_manager_id' => 'manager456',
        'temporary_upload' => 'false',
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->getKey(),
        'medium_id' => '123',
        'collection' => 'images',
        'file' => $this->getUploadedFile(), // create a temporary file resource
        'image_collection' => 'images',
        'document_collection' => 'docs',
        'youtube_collection' => 'youtube',
        'audio_collection' => 'audio',
        'video_collection' => 'video',
    ];

    $validator = Validator::make($data, $this->request->rules());
//    dd($validator->errors());
    expect($validator->passes())->toBeTrue();
});

it('fails validation when required fields are missing', function () {
    $data = [];

    $validator = Validator::make($data, $this->request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('initiator_id'))->toBeTrue();
    expect($validator->errors()->has('media_manager_id'))->toBeTrue();
    expect($validator->errors()->has('temporary_upload'))->toBeTrue();
    expect($validator->errors()->has('model_type'))->toBeTrue();
    expect($validator->errors()->has('medium_id'))->toBeTrue();
    expect($validator->errors()->has('collection'))->toBeTrue();
    expect($validator->errors()->has('file'))->toBeTrue();
    expect($validator->errors()->has('image_collection'))->toBeTrue();
});

it('fails validation if model_id is missing when temporary_upload is false', function () {
    $data = [
        'initiator_id' => 'user123',
        'media_manager_id' => 'manager456',
        'temporary_upload' => 'false',
        'model_type' => 'App\Models\Post',
        'medium_id' => '123',
        'collection' => 'images',
        'file' => tmpfile(),
        'image_collection' => 'images',
    ];

    $validator = Validator::make($data, $this->request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('model_id'))->toBeTrue();
});
