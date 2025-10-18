<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManager;

it('returns JSON success response when request expects JSON', function () {
    $request = Request::create('/test', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);

    $response = MediaResponse::success($request, 'initiator-123', 'media-manager-132', 'Operation succeeded', ['foo' => 'bar']);

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

    $response = MediaResponse::error($request, 'initiator-456', 'media-manager-150', 'Operation failed', ['errorCode' => 123]);

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

    $initiatorId = 'initiator-789';
    $mediaManagerId = 'media-manager-132';

    $response = MediaResponse::success(
        $request,
        $initiatorId,
        $mediaManagerId,
        'Redirect success'
    );

    $sessionData = ($response->getSession()->get('laravel-medialibrary-extensions.status'));

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($sessionData)->toMatchArray([
            'initiator_id' => $initiatorId,
            'media_manager_id' => $mediaManagerId,
            'type' => 'success',
            'message' => 'Redirect success',
        ]);
});

it('returns redirect error response when request does NOT expect JSON', function () {
    $request = Request::create('/test', 'GET');

    $initiatorId = 'initiator-789';
    $mediaManagerId = 'media-manager-132';

    $response = MediaResponse::error(
        $request,
        $initiatorId,
        $mediaManagerId,
        'Redirect error'
    );

    $sessionData = ($response->getSession()->get('laravel-medialibrary-extensions.status'));

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($sessionData)->toMatchArray([
            'initiator_id' => $initiatorId,
            'media_manager_id' => $mediaManagerId,
            'type' => 'error',
            'message' => 'Redirect error',
        ]);
});
