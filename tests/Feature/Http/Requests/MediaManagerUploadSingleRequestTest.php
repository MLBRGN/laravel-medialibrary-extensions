<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxMediaCount;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxTemporaryUploadCount;

beforeEach(function () {
    // Mock config values
    config()->set('medialibrary-extensions.upload_field_name', 'upload');
    config()->set('medialibrary-extensions.allowed_mimetypes', [
        ['image/jpeg', 'image/png'],
        ['video/mp4'],
    ]);
    config()->set('medialibrary-extensions.max_upload_size', 5000);
});

it('passes validation with required fields and at least one collection', function () {

    $model = $this->getTestBlogModel();
    $data = [
        'temporary_upload_mode' => 'true',
        'model_type' => $model->getMorphClass(),
        'collections' => ['image' => 'images'],
        'media' => null,
        'base_id' => 'user123',
        'data_source' => 'default',
    ];

    $request = new StoreSingleRequest;
    $request->merge($data);
    $validator = Validator::make($data, $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('fails validation when no collections are provided', function () {
    $model = $this->getTestBlogModel();
    $data = [
        'temporary_upload_mode' => 'true',
        'model_type' => $model->getMorphClass(),
        'media' => null,
        'base_id' => 'user123',
        'data_source' => 'default',
    ];

    $request = new StoreSingleRequest;
    $request->merge($data);
    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('collections'))->toBeTrue();
});

it('applies MaxMediaCount rule for non-temporary upload', function () {
    $model = $this->getTestBlogModel();
    $data = [
        'temporary_upload_mode' => 'false',
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->getKey(),
        'collections' => ['image' => 'images'],
        'media' => null,
        'base_id' => 'user123',
    ];

    $request = new StoreSingleRequest;
    $request->merge($data);
    $rules = $request->rules();
    $uploadRules = $rules['media'];

    $hasMaxMediaCountRule = collect($uploadRules)->contains(function ($rule) {
        return $rule instanceof MaxMediaCount;
    });

    expect($hasMaxMediaCountRule)->toBeTrue();
});

it('applies MaxTemporaryUploadCount rule for temporary upload', function () {
    $model = $this->getTestBlogModel();
    $data = [
        'temporary_upload_mode' => 'true',
        'model_type' => $model->getMorphClass(),
        'collections' => ['image' => 'images'],
        'media' => null,
        'base_id' => 'user123',
    ];

    $request = new StoreSingleRequest;
    $request->merge($data);
    $rules = $request->rules();
    $uploadRules = $rules['media'];

    $hasMaxTemporaryUploadCountRule = collect($uploadRules)->contains(function ($rule) {
        return $rule instanceof MaxTemporaryUploadCount;
    });

    expect($hasMaxTemporaryUploadCountRule)->toBeTrue();
});
