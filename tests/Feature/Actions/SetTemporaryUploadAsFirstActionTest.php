<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Actions\SetTemporaryUploadAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryUploadAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

it('fails when no collections provided JSON', function () {
    $baseId = 'initiator-123';
    $baseId = 'media-manager-123';

    $targetCollection = 'images';

    // Create request object
    $request = new SetTemporaryUploadAsFirstRequest([
        'base_id' => $baseId,
        'base_id' => $baseId,
        'target_media_collection' => $targetCollection,
        'medium_id' => 1,
        // collections intentionally missing
    ]);
    $request->headers->set('Accept', 'application/json');

    // Attach session manually
    $request->setLaravelSession(app('session')->driver());

    $mediaService = app(MediaService::class);
    $action = new SetTemporaryUploadAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    $data = $response->getData(true);

    expect($data)->toMatchArray([
        'baseId' => $baseId,
        'type' => 'error',
        'message' => __('medialibrary-extensions::messages.no_media_collections'),
    ]);

});

it('fails when no collections provided', function () {
    $baseId = 'initiator-123';
    $baseId = 'media-manager-123';
    $targetCollection = 'images';

    // Create request object
    $request = new SetTemporaryUploadAsFirstRequest([
        'base_id' => $baseId,
        'base_id' => $baseId,
        'target_media_collection' => $targetCollection,
        'medium_id' => 1,
        // collections intentionally missing
    ]);

    // Attach session manually
    $request->setLaravelSession(app('session')->driver());

    $mediaService = app(MediaService::class);
    $action = new SetTemporaryUploadAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $flashKey = config('medialibrary-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'base_id' => $baseId,
            'type' => 'error',
            'message' => __('medialibrary-extensions::messages.no_media_collections'),
        ]);

});

it('returns error when no media in collection JSON', function () {
    $baseId = 'initiator-123';
    $baseId = 'media-manager-123';
    $targetCollection = 'images';

    // Create request object
    $request = new SetTemporaryUploadAsFirstRequest([
        'base_id' => $baseId,
        'base_id' => $baseId,
        'target_media_collection' => $targetCollection,
        'medium_id' => 1,
        'collections' => ['image' => 'blog-non-existing-collection'],
    ]);

    $request->headers->set('Accept', 'application/json');

    // Attach session manually
    $request->setLaravelSession(app('session')->driver());

    $mediaService = app(MediaService::class);
    $action = new SetTemporaryUploadAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    $data = $response->getData(true);

    expect($data)->toMatchArray([
        'baseId' => $baseId,
        'type' => 'error',
        'message' => __('medialibrary-extensions::messages.no_media_collections'),
    ]);
});

it('returns error when no media in collection', function () {
    $baseId = 'initiator-123';
    $baseId = 'media-manager-123';
    $targetCollection = 'images';

    // Create request object
    $request = new SetTemporaryUploadAsFirstRequest([
        'base_id' => $baseId,
        'base_id' => $baseId,
        'target_media_collection' => $targetCollection,
        'medium_id' => 1,
        'collections' => ['image' => 'blog-non-existing-collection'],
    ]);

    // Attach session manually
    $request->setLaravelSession(app('session')->driver());

    $mediaService = app(MediaService::class);
    $action = new SetTemporaryUploadAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $flashKey = config('medialibrary-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'base_id' => $baseId,
            'type' => 'error',
            'message' => __('medialibrary-extensions::messages.no_media_collections'),
        ]);

});

it('can set as first in collection JSON', function () {

    $this->withSession([]); // boot a session in the test

    $baseId = 'initiator-123';
    $baseId = 'media-manager-123';
    $targetCollection = 'blog-images';

    $clientToken = (string) Str::ulid();
    $media1 = $this->getTemporaryUpload('temp1.jpg', [
        'collection_name' => $targetCollection,
        'custom_properties' => [],
        'client_token' => $clientToken,
        'instance_id' => \Mlbrgn\MediaLibraryExtensions\Support\InstanceManager::getInstanceId($baseId),
    ]);

    $media2 = $this->getTemporaryUpload('temp2.jpg', [
        'collection_name' => $targetCollection,
        'custom_properties' => [],
        'client_token' => $clientToken,
        'instance_id' => \Mlbrgn\MediaLibraryExtensions\Support\InstanceManager::getInstanceId($baseId),
    ]);

    // Create request object
    $request = new SetTemporaryUploadAsFirstRequest([
        'base_id' => $baseId,
        'base_id' => $baseId,
        'target_media_collection' => $targetCollection,
        'medium_id' => $media1->id,
        'collections' => ['image' => 'blog-images'],
        'client_token' => $clientToken,
    ]);
    $request->headers->set('Accept', 'application/json');

    //    dd(TemporaryUpload::all());

    // Attach session manually
    $request->setLaravelSession(app('session')->driver());

    $mediaService = app(MediaService::class);
    $action = new SetTemporaryUploadAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    $data = $response->getData(true);

    expect($data)->toMatchArray([
        'baseId' => $baseId,
        'type' => 'success',
        'message' => __('medialibrary-extensions::messages.medium_set_as_main'),
    ]);

    // Verify priorities in database
    $media1->refresh();
    $media2->refresh();

    expect($media1->getCustomProperty('priority'))->toBe(0);
    expect($media2->getCustomProperty('priority'))->toBe(1);

    expect($media1->order_column)->toBe(0);
    expect($media2->order_column)->toBe(1);
});

it('can set as first in collection', function () {

    $this->withSession([]); // boot a session in the test

    $baseId = 'initiator-123';
    $baseId = 'media-manager-123';
    $targetCollection = 'blog-images';

    $clientToken = (string) Str::ulid();
    $media1 = $this->getTemporaryUpload('temp1.jpg', [
        'collection_name' => $targetCollection,
        'custom_properties' => [],
        'client_token' => $clientToken,
        'instance_id' => \Mlbrgn\MediaLibraryExtensions\Support\InstanceManager::getInstanceId($baseId),
    ]);

    $media2 = $this->getTemporaryUpload('temp2.jpg', [
        'collection_name' => $targetCollection,
        'custom_properties' => [],
        'client_token' => $clientToken,
        'instance_id' => \Mlbrgn\MediaLibraryExtensions\Support\InstanceManager::getInstanceId($baseId),
    ]);

    // Create request object
    $request = new SetTemporaryUploadAsFirstRequest([
        'base_id' => $baseId,
        'base_id' => $baseId,
        'target_media_collection' => $targetCollection,
        'medium_id' => $media1->id,
        'collections' => ['image' => 'blog-images'],
        'client_token' => $clientToken,
    ]);

    //    dd(TemporaryUpload::all());

    // Attach session manually
    $request->setLaravelSession(app('session')->driver());

    $mediaService = app(MediaService::class);
    $action = new SetTemporaryUploadAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    //    dd($response);
    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $flashKey = config('medialibrary-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull();
    expect($flashData)->toMatchArray([
        'base_id' => $baseId,
        'type' => 'success',
        'message' => __('medialibrary-extensions::messages.medium_set_as_main'),
    ]);

});

it('can set as first in collection with null model_id', function () {

    $this->withSession([]); // boot a session in the test

    $baseId = 'initiator-123';
    $baseId = 'media-manager-123';
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
        'base_id' => $baseId,
        'base_id' => $baseId,
        'target_media_collection' => $targetCollection,
        'medium_id' => $media1->id,
        'collections' => ['image' => 'blog-images'],
        'model_type' => 'Mlbrgn\MediaLibraryExtensions\Models\demo\Alien',
        'model_id' => null,
    ]);
    $request->headers->set('Accept', 'application/json');

    // Attach session manually
    $request->setLaravelSession(app('session')->driver());

    $mediaService = app(MediaService::class);
    $action = new SetTemporaryUploadAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    $data = $response->getData(true);

    expect($data)->toMatchArray([
        'baseId' => $baseId,
        'type' => 'success',
        'message' => __('medialibrary-extensions::messages.medium_set_as_main'),
    ]);
})->todo('fix this test');
