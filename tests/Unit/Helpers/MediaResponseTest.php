<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;

it('returns JSON success response when request expects JSON', function () {
    $request = Request::create('/test', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);

    $response = MediaResponse::success($request, 'initiator-123', 'Operation succeeded', ['foo' => 'bar']);

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->getStatusCode())->toBe(200)
        ->and($response->getData(true))->toMatchArray([
            'initiatorId' => 'initiator-123',
            'type' => 'success',
            'message' => 'Operation succeeded',
            'foo' => 'bar',
        ]);
});

it('returns JSON error response when request expects JSON', function () {
    $request = Request::create('/test', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);

    $response = MediaResponse::error($request, 'initiator-456', 'Operation failed', ['errorCode' => 123]);

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->getData(true))->toMatchArray([
            'initiatorId' => 'initiator-456',
            'type' => 'error',
            'message' => 'Operation failed',
            'errorCode' => 123,
        ]);
});

it('returns redirect success response when request does NOT expect JSON', function () {
    $request = Request::create('/test', 'GET');

    $response = MediaResponse::success($request, 'initiator-789', 'Redirect success');

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and(session('status'))->toMatchArray([
            'initiatorId' => 'initiator-789',
            'type' => 'success',
            'message' => 'Redirect success',
        ]);
})->todo();

it('returns redirect error response when request does NOT expect JSON', function () {
    $request = Request::create('/test', 'GET');

    $response = MediaResponse::error($request, 'initiator-000', 'Redirect error');

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and(session('status'))->toMatchArray([
            'initiatorId' => 'initiator-000',
            'type' => 'error',
            'message' => 'Redirect error',
        ]);
})->todo();
