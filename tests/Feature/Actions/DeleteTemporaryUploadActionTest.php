<?php

use Mlbrgn\MediaLibraryExtensions\Actions\DeleteTemporaryUploadAction;

covers(DeleteTemporaryUploadAction::class);

it('returns error response when no collections provided JSON', function () {
    $user = $this->getUser();
    $initiatorId = 'initiator-123';
    $mediaManagerId = 'media-manager-123';

    // Create a temporary upload
    $upload = $this->createTemporaryUpload([
        'collection_name' => 'images',
        'custom_properties' => ['priority' => 0],
    ]);

    // Call your route with empty payload to trigger 422
    $response = $this->actingAs($user)->deleteJson(
        route(config('media-library-extensions.route_prefix') . '-temporary-upload-destroy', $upload),
        ['initiator_id' => $initiatorId,  'media_manager_id' => $mediaManagerId]
    );

    $response->assertStatus(422)
        ->assertJson([
            'initiatorId' => $initiatorId,
            'type' => 'error',
            'message' => 'The image collection field is required when none of video collection / audio collection / document collection / youtube collection are present.',
        ]);
});


it('returns error response when no collections provided Redirect', function () {
    $user = $this->getUser();
    $initiatorId = 'initiator-123';
    $mediaManagerId = 'media-manager-123';

    // Create a temporary upload
    $upload = $this->createTemporaryUpload([
        'collection_name' => 'images',
        'custom_properties' => ['priority' => 0],
    ]);

    $response = $this->actingAs($user)->delete(
        route(config('media-library-extensions.route_prefix') . '-temporary-upload-destroy', $upload),
        ['initiator_id' => $initiatorId,  'media_manager_id' => $mediaManagerId]
    );

    $response->assertStatus(302);
    $response->assertRedirect();

    $flashKey = config('media-library-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'initiator_id' => $initiatorId,
            'type' => 'error',
            'message' => 'The image collection field is required when none of video collection / audio collection / document collection / youtube collection are present.',
        ]);
});

it('deletes the temporary upload and returns JSON', function () {
    $user = $this->getUser();
    $initiatorId = 'initiator-123';
    $mediaManagerId = 'media-manager-123';
    $imageCollectionName = 'images';

//    // Dump all queries executed during the request
//    DB::listen(function ($query) {
//        dump($query->sql, $query->bindings);
//    });

    // Create a temporary upload
    $temporaryUpload = $this->createTemporaryUpload([
        'collection_name' => $imageCollectionName,
        'custom_properties' => ['priority' => 0],
    ]);

    $this->assertDatabaseHas('mle_temporary_uploads', ['file_name' => $temporaryUpload->file_name]);

    // Call your route with empty payload to trigger 422
    $route =  route(mle_prefix_route('temporary-upload-destroy'), $temporaryUpload);
    $response = $this->actingAs($user)->deleteJson(
        $route,
        [
            'initiator_id' => $initiatorId,
            'media_manager_id' => $mediaManagerId,
            'image_collection' => $imageCollectionName,
        ]
    );

    $response->assertStatus(200)
        ->assertJson([
            'initiatorId' => $initiatorId,
            'type' => 'success',
            'message' => __('media-library-extensions::messages.medium_removed'),
        ]);

    $this->assertDatabaseMissing('mle_temporary_uploads', ['file_name' => $temporaryUpload->file_name]);
});

it('deletes the temporary upload and returns redirect', function () {

    $user = $this->getUser();
    $initiatorId = 'initiator-123';
    $mediaManagerId = 'media-manager-123';
    $imageCollectionName = 'images';

//    // Dump all queries executed during the request
//    DB::listen(function ($query) {
//        dump($query->sql, $query->bindings);
//    });

    // Create a temporary upload
    $temporaryUpload = $this->createTemporaryUpload([
        'collection_name' => $imageCollectionName,
        'custom_properties' => ['priority' => 0],
    ]);

    $this->assertDatabaseHas('mle_temporary_uploads', ['file_name' => $temporaryUpload->file_name]);


    // Call your route with empty payload to trigger 422
    $route =  route(mle_prefix_route('temporary-upload-destroy'), $temporaryUpload);
    $response = $this->actingAs($user)->delete(
        $route,
        [
            'initiator_id' => $initiatorId,
            'media_manager_id' => $mediaManagerId,
            'image_collection' => $imageCollectionName,
        ]
    );

    $response->assertRedirect();

    $flashKey = config('media-library-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'initiator_id' => $initiatorId,
            'type' => 'success',
            'message' => __('media-library-extensions::messages.medium_removed'),
        ]);
    $this->assertDatabaseMissing('mle_temporary_uploads', ['file_name' => $temporaryUpload->file_name]);
});

it('reorders all temporary uploads on delete with dummy session id', function () {

    $user = $this->getUser();

    $sessionId = 'test-session-id';

    $imageCollectionName = 'images';
    $initiatorId = 'initiator-123';
    $mediaManagerId = 'media-manager-123';

    // Create temporary uploads with the dummy session ID
    $temporaryUpload1 = $this->createTemporaryUpload([
        'collection_name' => $imageCollectionName,
        'custom_properties' => ['priority' => 0],
        'session_id' => $sessionId,
    ]);
    $temporaryUpload2 = $this->createTemporaryUpload([
        'collection_name' => $imageCollectionName,
        'custom_properties' => ['priority' => 1],
        'session_id' => $sessionId,
    ]);
    $temporaryUpload3 = $this->createTemporaryUpload([
        'collection_name' => $imageCollectionName,
        'custom_properties' => ['priority' => 2],
        'session_id' => $sessionId,
    ]);

    $route = route(mle_prefix_route('temporary-upload-destroy'), $temporaryUpload2);

    // Pass a dummy session ID via request headers
    $response = $this->actingAs($user)
        ->delete($route, [
            'initiator_id' => $initiatorId,
            'media_manager_id' => $mediaManagerId,
            'image_collection' => $imageCollectionName,
        ],
        [
            'X-Test-Session-Id' => $sessionId,
        ]);

    $flashKey = config('media-library-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'initiator_id' => $initiatorId,
            'type' => 'success',
            'message' => __('media-library-extensions::messages.medium_removed'),
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
