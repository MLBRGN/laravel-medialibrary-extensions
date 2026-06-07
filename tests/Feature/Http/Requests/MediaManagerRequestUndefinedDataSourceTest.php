<?php

use Illuminate\Support\Facades\Route;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest;

it('normalizes undefined data_source to null', function () {
    Route::post('/test-route', function (GetMediaManagerPreviewerHTMLRequest $request) {
        return response()->json(['data_source' => $request->input('data_source')]);
    });

    $response = $this->postJson('/test-route', [
        'initiator_id' => 'test',
        'model_type' => 'test',
        'data_source' => 'undefined',
        'temporary_upload_mode' => 'true',
        'collections' => '[]',
        'selectable' => 'false',
        'multiple' => 'true',
        'disabled' => 'false',
        'readonly' => 'false',
    ]);

    $response->assertStatus(200);
    expect($response->json('data_source'))->toBeNull();
});

it('normalizes null string data_source to null', function () {
    Route::post('/test-route-null', function (GetMediaManagerPreviewerHTMLRequest $request) {
        return response()->json(['data_source' => $request->input('data_source')]);
    });

    $response = $this->postJson('/test-route-null', [
        'initiator_id' => 'test',
        'model_type' => 'test',
        'data_source' => 'null',
        'temporary_upload_mode' => 'true',
        'collections' => '[]',
        'selectable' => 'false',
        'multiple' => 'true',
        'disabled' => 'false',
        'readonly' => 'false',
    ]);

    $response->assertStatus(200);
    expect($response->json('data_source'))->toBeNull();
});

it('preserves valid data_source', function () {
    Route::post('/test-route-valid', function (GetMediaManagerPreviewerHTMLRequest $request) {
        return response()->json(['data_source' => $request->input('data_source')]);
    });

    $response = $this->postJson('/test-route-valid', [
        'initiator_id' => 'test',
        'model_type' => 'test',
        'data_source' => 'demo',
        'temporary_upload_mode' => 'true',
        'collections' => '[]',
        'selectable' => 'false',
        'multiple' => 'true',
        'disabled' => 'false',
        'readonly' => 'false',
    ]);

    $response->assertStatus(200);
    expect($response->json('data_source'))->toBe('demo');
});
