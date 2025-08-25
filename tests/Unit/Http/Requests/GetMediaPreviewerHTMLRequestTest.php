<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest;

it('passes validation when all required fields are present and at least one collection is set', function () {
    $data = [
        'initiator_id' => 'user123',
        'temporary_uploads' => 'true',
        'model_type' => 'App\\Models\\Post',
        'image_collection' => 'images',
        'destroy_enabled' => 'true',
        'set_as_first_enabled' => 'false',
        'show_order' => 'true',
    ];

    $request = new GetMediaPreviewerHTMLRequest();

    $validator = Validator::make($data, $request->rules());
    $request->withValidator($validator);

    expect($validator->passes())->toBeTrue();
});

it('fails validation when no collections are provided', function () {
    $data = [
        'initiator_id' => 'user123',
        'temporary_uploads' => 'true',
        'model_type' => 'App\\Models\\Post',
        'destroy_enabled' => 'true',
        'set_as_first_enabled' => 'false',
        'show_order' => 'true',
    ];

    $request = new GetMediaPreviewerHTMLRequest();

    $validator = Validator::make($data, $request->rules());
    $request->withValidator($validator);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('image_collection'))->toBeTrue();
    expect($validator->errors()->has('video_collection'))->toBeTrue();
    expect($validator->errors()->has('audio_collection'))->toBeTrue();
    expect($validator->errors()->has('document_collection'))->toBeTrue();
    expect($validator->errors()->has('youtube_collection'))->toBeTrue();
});

it('requires model_id when temporary_uploads is false', function () {
    $data = [
        'initiator_id' => 'user123',
        'temporary_uploads' => 'false',
        'model_type' => 'App\\Models\\Post',
        'image_collection' => 'images',
        'destroy_enabled' => 'true',
        'set_as_first_enabled' => 'false',
        'show_order' => 'true',
    ];

    $request = new GetMediaPreviewerHTMLRequest();

    $validator = Validator::make($data, $request->rules());
    $request->withValidator($validator);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('model_id'))->toBeTrue();
})->todo();

it('passes validation when model_id is provided and temporary_uploads is false', function () {
    $data = [
        'initiator_id' => 'user123',
        'temporary_uploads' => 'false',
        'model_type' => 'App\\Models\\Post',
        'model_id' => 42,
        'image_collection' => 'images',
        'destroy_enabled' => 'true',
        'set_as_first_enabled' => 'false',
        'show_order' => 'true',
    ];

    $request = new GetMediaPreviewerHTMLRequest();

    $validator = Validator::make($data, $request->rules());
    $request->withValidator($validator);

    expect($validator->passes())->toBeTrue();
});
