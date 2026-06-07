<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;

it('returns a json success response when request expects json', function () {
    $request = Request::create('/', 'POST');
    $request->headers->set('Accept', 'application/json');

    $response = MediaResponse::success(
        $request,
        'initiator-123',
        'manager-456',
        'Success message',
        ['extra' => 'data']
    );

    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getStatusCode())->toBe(200);
    expect($response->getData(true))->toMatchArray([
        'initiatorId' => 'initiator-123',
        'type' => 'success',
        'message' => 'Success message',
        'extra' => 'data',
    ]);
});

it('returns a json error response with 422 status', function () {
    $request = Request::create('/', 'POST');
    $request->headers->set('Accept', 'application/json');

    $response = MediaResponse::error(
        $request,
        'initiator-123',
        'manager-456',
        'Error message'
    );

    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getStatusCode())->toBe(422);
    expect($response->getData(true))->toMatchArray([
        'type' => 'error',
        'message' => 'Error message',
    ]);
});

it('returns a redirect success response when not expecting json', function () {
    $request = Request::create('/', 'POST');

    // Mock previous URL for redirect
    $this->from('http://localhost/previous');

    $response = MediaResponse::success(
        $request,
        'initiator-123',
        'manager-456',
        'Success message'
    );

    expect($response)->toBeInstanceOf(RedirectResponse::class);
    expect($response->getTargetUrl())->toContain('/previous#manager-456');

    $sessionData = session()->get(status_session_prefix());
    expect($sessionData)->toMatchArray([
        'initiator_id' => 'initiator-123',
        'media_manager_id' => 'manager-456',
        'type' => 'success',
        'message' => 'Success message',
    ]);
});

it('handles errors in redirect response and flashes to error bag', function () {
    $request = Request::create('/', 'POST');
    $this->from('/previous');

    $errors = ['file' => ['Too large']];
    $response = MediaResponse::error(
        $request,
        'initiator-123',
        'manager-456',
        'Error message',
        ['errors' => $errors]
    );

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $errorBag = session()->get('errors');
    expect($errorBag)->toBeInstanceOf(ViewErrorBag::class);
    expect($errorBag->getBag('default')->get('file'))->toContain('Too large');
});
