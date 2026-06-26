<?php

use Illuminate\Support\Facades\Validator;

// Legacy example removed: used initiator_id

// Legacy example removed: used initiator_id

// Legacy example removed: used initiator_id

// Legacy example removed: used initiator_id

//
//
// Legacy example removed: initiator_id
//
// it('requires model_id when temporary_upload_mode is false', function () {
//    $data = [
//        'base_id' => 'abc123',
//        'temporary_upload_mode' => 'false',
//        'model_type' => 'App\Models\Post',
//        'show_destroy_button' => 'true',
//        'show_set_as_first_button' => 'false',
//        'show_media_edit_button' => 'true',
//    ];
//
//    $request = new GetMediaPreviewerHTMLRequest();
//    $validator = Validator::make($data, $request->rules());
//    $request->withValidator($validator);
//
//    expect($validator->fails())->toBeTrue();
//    expect($validator->errors()->has('model_id'))->toBeTrue();
// });
//
// it('passes validation when model_id is present and temporary_upload_mode is false', function () {
//    $data = [
//        'initiator_id' => 'abc123',
//        'temporary_upload_mode' => 'false',
//        'model_type' => 'App\Models\Post',
//        'model_id' => '42',
//        'show_destroy_button' => 'true',
//        'show_set_as_first_button' => 'false',
//        'show_media_edit_button' => 'true',
//    ];
//
//    $request = new GetMediaPreviewerHTMLRequest();
//    $validator = Validator::make($data, $request->rules());
//    $request->withValidator($validator);
//
//    expect($validator->passes())->toBeTrue();
// });
//
// it('requires at least one media collection', function () {
//    $data = [
//        'initiator_id' => 'abc123',
//        'temporary_upload_mode' => 'true',
//        'model_type' => 'App\Models\Post',
//        'show_destroy_button' => 'true',
//        'show_set_as_first_button' => 'false',
//        'show_media_edit_button' => 'true',
//    ];
//
//    $request = new GetMediaPreviewerHTMLRequest();
//    $validator = Validator::make($data, $request->rules());
//    $request->withValidator($validator);
//
//    expect($validator->fails())->toBeTrue();
// });
//
// it('validates boolean fields are either true or false', function () {
//    $data = [
//        'initiator_id' => 'abc123',
//        'temporary_upload_mode' => 'true',
//        'model_type' => 'App\Models\Post',
//        'show_destroy_button' => 'maybe', // invalid
//        'show_set_as_first_button' => 'false',
//        'show_media_edit_button' => 'true',
//    ];
//
//    $request = new GetMediaPreviewerHTMLRequest();
//    $validator = Validator::make($data, $request->rules());
//    $request->withValidator($validator);
//
//    expect($validator->fails())->toBeTrue();
//    expect($validator->errors()->has('show_destroy_button'))->toBeTrue();
// });
//
// it('passes validation when all required fields are present and at least one collection is set', function () {
//    $data = [
//        'initiator_id' => 'user123',
//        'temporary_upload_mode' => 'true',
//        'model_type' => 'App\\Models\\Post',
//        'show_destroy_button' => 'true',
//        'show_set_as_first_button' => 'false',
//        'show_media_edit_button' => 'true',
//        'show_order' => 'true',
//    ];
//
//    $request = new GetMediaPreviewerHTMLRequest;
//
//    $validator = Validator::make($data, $request->rules());
//    $request->withValidator($validator);
//
//    expect($validator->passes())->toBeTrue();
// });
//
// it('fails validation when no collections are provided', function () {
//    $data = [
//        'initiator_id' => 'user123',
//        'temporary_upload_mode' => 'true',
//        'model_type' => 'App\\Models\\Post',
//        'show_destroy_button' => 'true',
//        'show_set_as_first_button' => 'false',
//        'show_media_edit_button' => 'true',
//        'show_order' => 'true',
//    ];
//
//    $request = new GetMediaPreviewerHTMLRequest;
//
//    $validator = Validator::make($data, $request->rules());
//    $request->withValidator($validator);
//
//    expect($validator->fails())->toBeTrue();
// });
//
// it('requires model_id when temporary_uploads_mode is false', function () {
//    $data = [
//        'initiator_id' => 'user123',
//        'temporary_upload_mode' => 'false',
//        'model_type' => 'App\\Models\\Post',
//        'show_destroy_button' => 'true',
//        'show_set_as_first_button' => 'false',
//        'show_media_edit_button' => 'true',
//        'show_order' => 'true',
//    ];
//
//    $request = new GetMediaPreviewerHTMLRequest;
//
//    $validator = Validator::make($data, $request->rules());
//    $request->withValidator($validator);
//
//    expect($validator->fails())->toBeTrue();
//    expect($validator->errors()->has('model_id'))->toBeTrue();
// })->todo();
//
// it('passes validation when model_id is provided and temporary_upload_mode is false', function () {
//    $data = [
//        'initiator_id' => 'user123',
//        'temporary_upload_mode' => 'false',
//        'model_type' => 'App\\Models\\Post',
//        'model_id' => 42,
//        'show_destroy_button' => 'true',
//        'show_set_as_first_button' => 'false',
//        'show_media_edit_button' => 'true',
//        'show_order' => 'true',
//    ];
//
//    $request = new GetMediaPreviewerHTMLRequest;
//
//    $validator = Validator::make($data, $request->rules());
//    $request->withValidator($validator);
//
//    dd($validator);
//    expect($validator->passes())->toBeTrue();
// });
