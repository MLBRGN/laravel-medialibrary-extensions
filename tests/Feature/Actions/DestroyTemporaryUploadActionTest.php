<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\DestroyTemporaryUploadAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyTemporaryUploadRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;

covers(DestroyTemporaryUploadAction::class);

it('returns error response when no collections provided (JSON)', function () {
    $user = $this->getUser();
    $baseId = 'initiator-123';

    // Create a temporary upload
    $upload = TemporaryUpload::create([
        'collection_name' => 'images',
        'custom_properties' => ['priority' => 0],
        'disk' => 'public',
        'path' => 'test.png',
        'name' => 'test',
        'file_name' => 'test.png',
        'mime_type' => 'image/png',
        'size' => 123,
        'client_token' => session()->getId(),
        'instance_id' => InstanceManager::getInstanceId($baseId),
    ]);

    // Call your route with an empty payload to trigger 422
    $response = $this->withCookie('mle_client_token', session()->getId())
        ->actingAs($user)->deleteJson(
        route(config('medialibrary-extensions.route_prefix').'-destroy-temporary-upload', $upload),
        ['base_id' => $baseId]
    );

    $response->assertStatus(422)
        ->assertJson([
            'baseId' => $baseId,
            'type' => 'error',
            'message' => 'The collections field is required.',
        ]);
});

it('returns error response when no collections provided (Redirect)', function () {
    $user = $this->getUser();
    $baseId = 'initiator-123';

    // Create a temporary upload
    $upload = TemporaryUpload::create([
        'collection_name' => 'images',
        'custom_properties' => ['priority' => 0],
        'disk' => 'public',
        'path' => 'test.png',
        'name' => 'test',
        'file_name' => 'test.png',
        'mime_type' => 'image/png',
        'size' => 123,
        'client_token' => session()->getId(),
        'instance_id' => InstanceManager::getInstanceId($baseId),
    ]);


    $response = $this->withCookie('mle_client_token', session()->getId())
        ->actingAs($user)->delete(
        route(config('medialibrary-extensions.route_prefix').'-destroy-temporary-upload', $upload),
        ['base_id' => $baseId]
    );

    $response->assertStatus(422);
//    $response->assertRedirect();

    $flashKey = config('medialibrary-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'base_id' => $baseId,
            'type' => 'error',
            'message' => 'The collections field is required.',
        ]);
});

it('deletes the temporary upload and returns JSON', function () {
    $user = $this->getUser();
    $baseId = 'initiator-123';
    $imageCollectionName = 'images';
    $clientToken = 'test-session-id-json';

    $temporaryUpload = TemporaryUpload::create([
        'collection_name' => $imageCollectionName,
        'custom_properties' => ['priority' => 0],
        'disk' => 'public',
        'path' => 'test.png',
        'name' => 'test',
        'file_name' => 'test.png',
        'mime_type' => 'image/png',
        'size' => 123,
        'client_token' => $clientToken,
        'instance_id' => InstanceManager::getInstanceId($baseId),
    ]);

    $this->assertDatabaseHas('mle_temporary_uploads', ['file_name' => $temporaryUpload->file_name]);

    // Call your route
    $route = route(mle_prefix_route('destroy-temporary-upload'), $temporaryUpload);
    $response = $this
        ->withCookie('mle_client_token', $clientToken)
        ->actingAs($user)
        ->deleteJson(
            $route,
            [
                'base_id' => $baseId,
                'collections' => ['image' => 'images'],
                'client_token' => $clientToken,
            ]
        );

    $response->assertStatus(200)
        ->assertJson([
            'baseId' => $baseId,
            'type' => 'success',
            'message' => __('medialibrary-extensions::messages.medium_removed'),
        ]);

    $this->assertDatabaseMissing('mle_temporary_uploads', ['file_name' => $temporaryUpload->file_name]);
});

it('deletes the temporary upload and returns redirect', function () {
    $user = $this->getUser();
    $baseId = 'initiator-123';
    $collections = ['image' => 'images'];
    $clientToken = 'test-session-id-redirect';

    // Create a temporary upload
    $temporaryUpload = TemporaryUpload::create([
        'collection_name' => $collections['image'],
        'custom_properties' => ['priority' => 0],
        'disk' => 'public',
        'path' => 'test.png',
        'name' => 'test',
        'file_name' => 'test.png',
        'mime_type' => 'image/png',
        'size' => 123,
        'client_token' => $clientToken,
        'instance_id' => InstanceManager::getInstanceId($baseId),
    ]);

    $this->assertDatabaseHas('mle_temporary_uploads', ['file_name' => $temporaryUpload->file_name]);

    // Call your route
    $route = route(mle_prefix_route('destroy-temporary-upload'), $temporaryUpload);
    $response = $this->withCookie('mle_client_token', $clientToken)
        ->actingAs($user)->delete(
        $route,
        [
            'base_id' => $baseId,
            'collections' => $collections,
            'client_token' => $clientToken,
        ]
    );

    $response->assertRedirect();

    $flashKey = config('medialibrary-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'base_id' => $baseId,
            'type' => 'success',
            'message' => __('medialibrary-extensions::messages.medium_removed'),
        ]);
    $this->assertDatabaseMissing('mle_temporary_uploads', ['file_name' => $temporaryUpload->file_name]);
});

it('reorders all temporary uploads on delete with dummy session id', function () {
    $user = $this->getUser();

    $clientToken = 'test-session-id';
    $collections = ['image' => 'images'];
    $baseId = 'initiator-123';

    // Create temporary uploads with the dummy session ID
    $temporaryUpload1 = TemporaryUpload::create([
        'collection_name' => $collections['image'],
        'custom_properties' => ['priority' => 0],
        'client_token' => $clientToken,
        'disk' => 'public',
        'path' => 'test1.png',
        'name' => 'test1',
        'file_name' => 'test1.png',
        'mime_type' => 'image/png',
        'size' => 123,
        'instance_id' => InstanceManager::getInstanceId($baseId),
    ]);
    $temporaryUpload2 = TemporaryUpload::create([
        'collection_name' => $collections['image'],
        'custom_properties' => ['priority' => 1],
        'client_token' => $clientToken,
        'disk' => 'public',
        'path' => 'test2.png',
        'name' => 'test2',
        'file_name' => 'test2.png',
        'mime_type' => 'image/png',
        'size' => 123,
        'instance_id' => InstanceManager::getInstanceId($baseId),
    ]);
    $temporaryUpload3 = TemporaryUpload::create([
        'collection_name' => $collections['image'],
        'custom_properties' => ['priority' => 2],
        'client_token' => $clientToken,
        'disk' => 'public',
        'path' => 'test3.png',
        'name' => 'test3',
        'file_name' => 'test3.png',
        'mime_type' => 'image/png',
        'size' => 123,
        'instance_id' => InstanceManager::getInstanceId($baseId),
    ]);

    $route = route(mle_prefix_route('destroy-temporary-upload'), $temporaryUpload2);

    // Pass a dummy session ID via request
    $response = $this->actingAs($user)
        ->delete($route, [
            'base_id' => $baseId,
            'collections' => $collections,
            'client_token' => $clientToken,
        ]);

    $flashKey = config('medialibrary-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'base_id' => $baseId,
            'type' => 'success',
            'message' => __('medialibrary-extensions::messages.medium_removed'),
        ]);

    // Ensure the deleted upload is gone, others remain
    $this->assertDatabaseHas('mle_temporary_uploads', ['id' => $temporaryUpload1->id]);
    $this->assertDatabaseMissing('mle_temporary_uploads', ['id' => $temporaryUpload2->id]);
    $this->assertDatabaseHas('mle_temporary_uploads', ['id' => $temporaryUpload3->id]);

    // Refresh and check priorities
    $temporaryUpload1->refresh();
    $temporaryUpload3->refresh();

    expect($temporaryUpload1->getCustomProperty('priority'))->toBe(0);
    expect($temporaryUpload3->getCustomProperty('priority'))->toBe(1);
});

it('deletes the temporary upload via action execute (JSON)', function () {
    $baseId = 'initiator-123';
    $temporaryUpload = $this->getTemporaryUpload('temp.jpg', [
        'client_token' => session()->getId(),
        'instance_id' => InstanceManager::getInstanceId($baseId),
    ]);

    expect(TemporaryUpload::find($temporaryUpload->id))->not()->toBeNull();

    $request = DestroyTemporaryUploadRequest::create(
        '/dummy-url',
        'DELETE',
        [],
        [],
        [],
        [],
        null
    );
    $request->merge([
        'temporaryUploadId' => $temporaryUpload->id,
        'base_id' => $baseId,
        'image_collection' => 'images',
        'collections' => ['image' => 'images'],
        'client_token' => session()->getId(),
    ]);

    // Force expectsJson = true
    $request->headers->set('Accept', 'application/json');

    $action = app(DestroyTemporaryUploadAction::class);

    $response = $action->execute($request);

    // Assert the model was deleted
    expect(TemporaryUpload::find($temporaryUpload->id))->toBeNull();
    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getData(true))->toMatchArray([
        'baseId' => $baseId,
        'type' => 'success',
        'message' => __('medialibrary-extensions::messages.medium_removed'),
    ]);
});

it('deletes the temporary upload via action execute (Redirect)', function () {
    $baseId = 'initiator-456';
    $temporaryUpload = $this->getTemporaryUpload('temp.jpg', [
        'client_token' => session()->getId(),
        'instance_id' => InstanceManager::getInstanceId($baseId),
    ]);

    expect(TemporaryUpload::find($temporaryUpload->id))->not()->toBeNull();

    $request = DestroyTemporaryUploadRequest::create('/dummy-url', 'DELETE');
    $request->merge([
        'temporaryUploadId' => $temporaryUpload->id,
        'base_id' => $baseId,
        'image_collection' => 'images',
        'collections' => ['image' => 'images'],
        'client_token' => session()->getId(),
    ]);

    $action = app(DestroyTemporaryUploadAction::class);

    $response = $action->execute($request, $temporaryUpload);

    // Assert the model was deleted
    expect(TemporaryUpload::find($temporaryUpload->id))->toBeNull();
    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $flashKey = config('medialibrary-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull();

    expect($flashData)->toMatchArray([
        'base_id' => 'initiator-456',
        'type' => 'success',
        'message' => __('medialibrary-extensions::messages.medium_removed'),
    ]);
});
