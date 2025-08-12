<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\DeleteTemporaryUploadAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerTemporaryUploadDestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

it('deletes the temporary upload and returns a JSON response when request expects JSON', function () {
    $temporaryUpload =TemporaryUpload::create([
        'disk' => 'media',
        'path' => 'uploads/temp.jpg',
        'name' => 'temp',
        'file_name' => 'temp.jpg',
        'collection_name' => 'test',
        'custom_properties' => ['image_collection' => 'images'],
        'session_id' => session()->getId(),
    ]);

    $request = MediaManagerTemporaryUploadDestroyRequest::create('/dummy-url', 'DELETE', [], [], [], [], null);
    $request->merge(['initiator_id' => 'initiator-123']);

    // Force expectsJson = true
    $request->headers->set('Accept', 'application/json');

    $action = new DeleteTemporaryUploadAction();

    $response = $action->execute($request, $temporaryUpload);

    // Assert the model was deleted
    expect(TemporaryUpload::find($temporaryUpload->id))->toBeNull();
    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getData(true))->toMatchArray([
        'initiatorId' => 'initiator-123',
        'type' => 'success',
        'message' => __('media-library-extensions::messages.medium_removed'),
    ]);
});

it('deletes the temporary upload and returns a redirect response with flash data when request does NOT expect JSON', function () {
    $temporaryUpload = TemporaryUpload::create([
        'disk' => 'media',
        'path' => 'uploads/temp.jpg',
        'name' => 'temp',
        'file_name' => 'temp.jpg',
        'collection_name' => 'test',
        'custom_properties' => ['image_collection' => 'images'],
        'session_id' => session()->getId(),
    ]);

    $request = MediaManagerTemporaryUploadDestroyRequest::create('/dummy-url', 'DELETE');
    $request->merge(['initiator_id' => 'initiator-456']);

    // No 'Accept: application/json' header => expectsJson is false

    $action = new DeleteTemporaryUploadAction();

    $response = $action->execute($request, $temporaryUpload);

    // Assert the model was deleted
    expect(TemporaryUpload::find($temporaryUpload->id))->toBeNull();
    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $flashKey = config('media-library-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull();

    expect($flashData)->toMatchArray([
        'initiatorId' => 'initiator-456',
        'type' => 'success',
        'message' => __('media-library-extensions::messages.medium_removed'),
    ]);
});
