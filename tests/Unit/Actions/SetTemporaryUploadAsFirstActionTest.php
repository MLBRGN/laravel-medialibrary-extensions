<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\SetTemporaryUploadAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryUploadAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

it('fails when no collections provided JSON', function () {
    $initiatorId = 'initiator-123';
    $targetCollection = 'images';

    // Create request object
    $request = new SetTemporaryUploadAsFirstRequest([
        'initiator_id' => $initiatorId,
        'target_media_collection' => $targetCollection,
        'medium_id' => 1,
    ]);
    $request->headers->set('Accept', 'application/json');

    // Attach session manually
    $request->setLaravelSession(app('session')->driver());

    $mediaService = new MediaService();
    $action = new SetTemporaryUploadAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    $data = $response->getData(true);

    expect($data)->toMatchArray([
        'initiatorId' => $initiatorId,
        'type' => 'error',
        'message' => __('media-library-extensions::messages.no_media_collections'),
    ]);

});

it('fails when no collections provided', function () {
    $initiatorId = 'initiator-123';
    $targetCollection = 'images';

    // Create request object
    $request = new SetTemporaryUploadAsFirstRequest([
        'initiator_id' => $initiatorId,
        'target_media_collection' => $targetCollection,
        'medium_id' => 1,
    ]);

    // Attach session manually
    $request->setLaravelSession(app('session')->driver());

    $mediaService = new MediaService();
    $action = new SetTemporaryUploadAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $flashKey = config('media-library-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'initiator_id' => $initiatorId,
            'type' => 'error',
            'message' => __('media-library-extensions::messages.no_media_collections'),
        ]);

});

it('returns error when no media in collection JSON', function () {
    $initiatorId = 'initiator-123';
    $targetCollection = 'images';

    // Create request object
    $request = new SetTemporaryUploadAsFirstRequest([
        'initiator_id' => $initiatorId,
        'target_media_collection' => $targetCollection,
        'medium_id' => 1,
        'image_collection' => 'blog-non-existing-collection',
    ]);

    $request->headers->set('Accept', 'application/json');

    // Attach session manually
    $request->setLaravelSession(app('session')->driver());

    $mediaService = new MediaService();
    $action = new SetTemporaryUploadAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    $data = $response->getData(true);

    expect($data)->toMatchArray([
        'initiatorId' => $initiatorId,
        'type' => 'error',
        'message' => __('media-library-extensions::messages.no_media'),
    ]);
});


it('returns error when no media in collection', function () {
    $initiatorId = 'initiator-123';
    $targetCollection = 'images';

    // Create request object
    $request = new SetTemporaryUploadAsFirstRequest([
        'initiator_id' => $initiatorId,
        'target_media_collection' => $targetCollection,
        'medium_id' => 1,
        'image_collection' => 'blog-non-existing-collection',
    ]);

    // Attach session manually
    $request->setLaravelSession(app('session')->driver());

    $mediaService = new MediaService();
    $action = new SetTemporaryUploadAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $flashKey = config('media-library-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'initiator_id' => $initiatorId,
            'type' => 'error',
            'message' => __('media-library-extensions::messages.no_media'),
        ]);

});

it('can set as first in collection JSON', function () {

    $this->withSession([]); // boot a session in the test

    $initiatorId = 'initiator-123';
    $targetCollection = 'blog-images';

    $media1 = $this->getTemporaryUpload('temp1.jpg', [
        'collection_name' => $targetCollection,
        'custom_properties' => [],
    ]);

    $media2 = $this->getTemporaryUpload('temp2.jpg', [
        'collection_name' => $targetCollection,
        'custom_properties' => [],
    ]);

    // Create request object
    $request = new SetTemporaryUploadAsFirstRequest([
        'initiator_id' => $initiatorId,
        'target_media_collection' => $targetCollection,
        'medium_id' => $media1->id,
        'image_collection' => 'blog-images',
    ]);
    $request->headers->set('Accept', 'application/json');

//    dd(TemporaryUpload::all());

    // Attach session manually
    $request->setLaravelSession(app('session')->driver());

    $mediaService = new MediaService();
    $action = new SetTemporaryUploadAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    $data = $response->getData(true);

    expect($data)->toMatchArray([
        'initiatorId' => $initiatorId,
        'type' => 'success',
        'message' => __('media-library-extensions::messages.medium_set_as_main'),
    ]);

});

it('can set as first in collection', function () {

    $this->withSession([]); // boot a session in the test

    $initiatorId = 'initiator-123';
    $targetCollection = 'blog-images';

    $media1 = $this->getTemporaryUpload('temp1.jpg', [
        'collection_name' => $targetCollection,
        'custom_properties' => [],
    ]);

    $media2 = $this->getTemporaryUpload('temp2.jpg', [
        'collection_name' => $targetCollection,
        'custom_properties' => [],
    ]);

    // Create request object
    $request = new SetTemporaryUploadAsFirstRequest([
        'initiator_id' => $initiatorId,
        'target_media_collection' => $targetCollection,
        'medium_id' => $media1->id,
        'image_collection' => 'blog-images',
    ]);

//    dd(TemporaryUpload::all());

    // Attach session manually
    $request->setLaravelSession(app('session')->driver());

    $mediaService = new MediaService();
    $action = new SetTemporaryUploadAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

//    dd($response);
    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $flashKey = config('media-library-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull();
    expect($flashData)->toMatchArray([
        'initiator_id' => $initiatorId,
        'type' => 'success',
        'message' => __('media-library-extensions::messages.medium_set_as_main'),
    ]);

});
