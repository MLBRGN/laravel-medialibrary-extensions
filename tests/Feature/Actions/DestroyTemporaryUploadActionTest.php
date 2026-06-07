<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\DestroyTemporaryUploadAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyTemporaryUploadRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

it('deletes the temporary upload and returns a JSON response when request expects JSON', function () {
    $temporaryUpload = $this->getTemporaryUpload('temp.jpg', [
        'session_id' => session()->getId(),
    ]);

    expect(TemporaryUpload::find($temporaryUpload->id))->not()->toBeNull();

    $request = DestroyTemporaryUploadRequest::create('/dummy-url', 'DELETE', [], [], [], [], null);
    $request->merge([
        'temporaryUploadId' => $temporaryUpload->id,
        'initiator_id' => 'initiator-123',
        'media_manager_id' => 'media-manager-123',
        'image_collection' => 'images', // TODO
    ]);

    // Force expectsJson = true
    $request->headers->set('Accept', 'application/json');

    $action = app(DestroyTemporaryUploadAction::class);

    $response = $action->execute($request, $temporaryUpload);

    // Assert the model was deleted
    expect(TemporaryUpload::find($temporaryUpload->id))->toBeNull();
    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getData(true))->toMatchArray([
        'initiatorId' => 'initiator-123',
        'type' => 'success',
        'message' => __('medialibrary-extensions::messages.medium_removed'),
    ]);
});

it('deletes the temporary upload and returns a redirect response with flash data when request does NOT expect JSON', function () {
    $temporaryUpload = $this->getTemporaryUpload('temp.jpg', [
        'session_id' => session()->getId(),
    ]);

    expect(TemporaryUpload::find($temporaryUpload->id))->not()->toBeNull();

    $request = DestroyTemporaryUploadRequest::create('/dummy-url', 'DELETE');
    $request->merge([
        'temporaryUploadId' => $temporaryUpload->id,
        'initiator_id' => 'initiator-456',
        'media_manager_id' => 'media-manager-123',
        'image_collection' => 'images', // TODO
    ]);

    // No 'Accept: application/json' header => expectsJson is false

    $action = app(DestroyTemporaryUploadAction::class);

    $response = $action->execute($request, $temporaryUpload);

    // Assert the model was deleted
    expect(TemporaryUpload::find($temporaryUpload->id))->toBeNull();
    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $flashKey = config('medialibrary-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull();

    expect($flashData)->toMatchArray([
        'initiator_id' => 'initiator-456',
        'type' => 'success',
        'message' => __('medialibrary-extensions::messages.medium_removed'),
    ]);
});
