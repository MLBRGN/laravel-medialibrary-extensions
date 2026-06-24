<?php

use Mlbrgn\MediaLibraryExtensions\Actions\DestroyTemporaryUploadAction;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

covers(DestroyTemporaryUploadAction::class);

it('returns error response when no collections provided (JSON)', function () {
    $user = $this->getUser();
    $initiatorId = 'initiator-123';
    $mediaManagerDomId = 'media-manager-123';

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
    ]);

    // Call your route with empty payload to trigger 422
    $response = $this->actingAs($user)->deleteJson(
        route(config('medialibrary-extensions.route_prefix').'-destroy-temporary-upload', $upload),
        ['initiator_id' => $initiatorId,  'media_manager_id' => $mediaManagerDomId]
    );

    $response->assertStatus(422)
        ->assertJson([
            'initiatorId' => $initiatorId,
            'type' => 'error',
            'message' => 'The collections field is required.', // TODO no static strings
        ]);
});

it('returns error response when no collections provided Redirect', function () {
    $user = $this->getUser();
    $initiatorId = 'initiator-123';
    $mediaManagerDomId = 'media-manager-123';

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
    ]);

    $response = $this->actingAs($user)->delete(
        route(config('medialibrary-extensions.route_prefix').'-destroy-temporary-upload', $upload),
        ['initiator_id' => $initiatorId,  'media_manager_id' => $mediaManagerDomId]
    );

    $response->assertStatus(302);
    $response->assertRedirect();

    $flashKey = config('medialibrary-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'initiator_id' => $initiatorId,
            'type' => 'error',
            'message' => 'The collections field is required.', // TODO no static strings
        ]);
});

it('deletes the temporary upload and returns JSON', function () {
    $user = $this->getUser();
    $initiatorId = 'initiator-123';
    $mediaManagerDomId = 'media-manager-123';
    $imageCollectionName = 'images';

    //    // Dump all queries executed during the request
    //    DB::listen(function ($query) {
    //        dump($query->sql, $query->bindings);
    //    });

    $temporaryUpload = TemporaryUpload::create([
        'collection_name' => $imageCollectionName,
        'custom_properties' => ['priority' => 0],
        'disk' => 'public',
        'path' => 'test.png',
        'name' => 'test',
        'file_name' => 'test.png',
        'mime_type' => 'image/png',
        'size' => 123,
        'client_token' => session()->getId(),
    ]);

    $this->assertDatabaseHas('mle_temporary_uploads', ['file_name' => $temporaryUpload->file_name]);

    // Call your route with empty payload to trigger 422
    $route = route(mle_prefix_route('destroy-temporary-upload'), $temporaryUpload);
    $response = $this
        ->actingAs($user)
        ->deleteJson(
            $route,
            [
                'initiator_id' => $initiatorId,
                'media_manager_id' => $mediaManagerDomId,
                'collections' => ['image' => 'images'],
            ]
        );

    $response->assertStatus(200)
        ->assertJson([
            'initiatorId' => $initiatorId,
            'type' => 'success',
            'message' => __('medialibrary-extensions::messages.medium_removed'),
        ]);

    $this->assertDatabaseMissing('mle_temporary_uploads', ['file_name' => $temporaryUpload->file_name]);
});

it('deletes the temporary upload and returns redirect', function () {

    $user = $this->getUser();
    $initiatorId = 'initiator-123';
    $mediaManagerDomId = 'media-manager-123';
    $collections = ['image' => 'images'];

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
        'client_token' => session()->getId(),
    ]);

    $this->assertDatabaseHas('mle_temporary_uploads', ['file_name' => $temporaryUpload->file_name]);

    // Call your route with empty payload to trigger 422
    $route = route(mle_prefix_route('destroy-temporary-upload'), $temporaryUpload);
    $response = $this->actingAs($user)->delete(
        $route,
        [
            'initiator_id' => $initiatorId,
            'media_manager_id' => $mediaManagerDomId,
            'collections' => $collections,
        ]
    );

    $response->assertRedirect();

    $flashKey = config('medialibrary-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'initiator_id' => $initiatorId,
            'type' => 'success',
            'message' => __('medialibrary-extensions::messages.medium_removed'),
        ]);
    $this->assertDatabaseMissing('mle_temporary_uploads', ['file_name' => $temporaryUpload->file_name]);
});

it('reorders all temporary uploads on delete with dummy session id', function () {

    $user = $this->getUser();

    $clientToken = 'test-session-id';
    $collections = ['image' => 'images'];
    $initiatorId = 'initiator-123';
    $mediaManagerDomId = 'media-manager-123';

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
    ]);

    $route = route(mle_prefix_route('destroy-temporary-upload'), $temporaryUpload2);

    // Pass a dummy session ID via request
    $response = $this->actingAs($user)
        ->delete($route, [
            'initiator_id' => $initiatorId,
            'media_manager_id' => $mediaManagerDomId,
            'collections' => $collections,
            'client_token' => $clientToken,
        ]);

    $flashKey = config('medialibrary-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'initiator_id' => $initiatorId,
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
