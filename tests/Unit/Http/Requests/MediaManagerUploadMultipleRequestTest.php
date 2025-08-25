<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxMediaCount;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxTemporaryUploadCount;

beforeEach(function () {
    // Mock config values
    config()->set('media-library-extensions.upload_field_name_multiple', 'uploads');
    config()->set('media-library-extensions.max_items_in_shared_media_collections', 10);
    config()->set('media-library-extensions.allowed_mimetypes', [
        ['image/jpeg', 'image/png'],
        ['video/mp4'],
    ]);
    config()->set('media-library-extensions.max_upload_size', 5000);
});

it('passes validation with required fields and at least one collection', function () {
    $model = $this->getTestBlogModel();
    $data = [
        'temporary_upload' => 'false',
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->getKey(),
        'image_collection' => 'images',
        'uploads' => [],
        'initiator_id' => 'user123',
        'media_manager_id' => 'manager456',
    ];

    $request = new MediaManagerUploadMultipleRequest();
    $request->merge($data);
    $validator = Validator::make($data, $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('fails validation when no collections are provided', function () {
    $model = $this->getTestBlogModel();
    $data = [
        'temporary_upload' => 'true',
        'model_type' => $model->getMorphClass(),
        'uploads' => [],
        'initiator_id' => 'user123',
        'media_manager_id' => 'manager456',
    ];

    $request = new MediaManagerUploadMultipleRequest();
    $request->merge($data);
    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('image_collection'))->toBeTrue();
    expect($validator->errors()->has('video_collection'))->toBeTrue();
    expect($validator->errors()->has('audio_collection'))->toBeTrue();
    expect($validator->errors()->has('document_collection'))->toBeTrue();
    expect($validator->errors()->has('youtube_collection'))->toBeTrue();
});

it('applies MaxMediaCount rule for non-temporary uploads', function () {
    $model = $this->getTestBlogModel();
    $data = [
        'temporary_upload' => 'false',
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->getKey(),
        'image_collection' => 'images',
        'uploads' => [],
        'initiator_id' => 'user123',
        'media_manager_id' => 'manager456',
    ];

    $request = new MediaManagerUploadMultipleRequest();
    $request->merge($data);
    $rules = $request->rules();

    $uploadRules = $rules['uploads'];

    $hasMaxMediaCountRule = collect($uploadRules)->contains(function ($rule) {
        return $rule instanceof MaxMediaCount;
    });

    expect($hasMaxMediaCountRule)->toBeTrue();
});

it('applies MaxTemporaryUploadCount rule for temporary uploads', function () {
    $model = $this->getTestBlogModel();
    $data = [
        'temporary_upload' => 'true',
        'model_type' => $model->getMorphClass(),
        'image_collection' => 'images',
        'uploads' => [],
        'initiator_id' => 'user123',
        'media_manager_id' => 'manager456',
    ];

    $request = new MediaManagerUploadMultipleRequest();
    $request->merge($data);
    $rules = $request->rules();

    $uploadRules = $rules['uploads'];

    $hasMaxTemporaryUploadCountRule = collect($uploadRules)->contains(function ($rule) {
        return $rule instanceof MaxTemporaryUploadCount;
    });

    expect($hasMaxTemporaryUploadCountRule)->toBeTrue();
});
