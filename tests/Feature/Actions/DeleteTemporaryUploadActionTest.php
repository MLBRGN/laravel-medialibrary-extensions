<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\DeleteTemporaryUploadAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerTemporaryUploadDestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Tests\Database\Factories\TemporaryUploadFactory;

covers(DeleteTemporaryUploadAction::class);

it('returns error response when no collections provided JSON', function () {
    // Disable auth middleware for this test
    $this->withoutMiddleware();

    $initiatorId = 'initiator-123';
    $mediaManagerId = 'media-manager-123';

    // Create a temporary upload
    $upload = $this->createTemporaryUpload([
        'collection_name' => 'images',
        'custom_properties' => ['priority' => 0],
    ]);

    // Call your route with empty payload to trigger 422
    $response = $this->deleteJson(
        route(config('media-library-extensions.route_prefix') . '-temporary-upload-destroy', $upload),
        ['initiator_id' => $initiatorId,  'media_manager_id' => $mediaManagerId]
    );

    $response->assertStatus(422)
        ->assertJson([
            'initiatorId' => $initiatorId,
            'type' => 'error',
            'message' => __('media-library-extensions::messages.no_media_collections'),
        ]);
});


it('returns error response when no collections provided Redirect', function () {
    // Disable auth middleware for this test
    $this->withoutMiddleware();

    $initiatorId = 'initiator-123';
    $mediaManagerId = 'media-manager-123';

    // Create a temporary upload
    $upload = $this->createTemporaryUpload([
        'collection_name' => 'images',
        'custom_properties' => ['priority' => 0],
    ]);

    $response = $this->delete(
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
            'message' => __('media-library-extensions::messages.no_media_collections'),
        ]);
});

it('deletes the temporary upload and returns redirect with flash data when request does NOT expect JSON', function () {
    $temporaryUpload = $this->getTemporaryUpload('temp.jpg', ['session_id' => session()->getId()]);

    $request = MediaManagerTemporaryUploadDestroyRequest::create('/dummy-url', 'DELETE');
    $request->merge([
        'initiator_id' => 'initiator-456',
        'media_manager_id' => 'media-manager-123',
    ]);

    $action = new DeleteTemporaryUploadAction();
    $response = $action->execute($request, $temporaryUpload);

    expect(TemporaryUpload::find($temporaryUpload->id))->toBeNull();
    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $flashKey = config('media-library-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'initiator_id' => 'initiator-456',
            'type' => 'success',
            'message' => __('media-library-extensions::messages.medium_removed'),
        ]);
})->todo();

it('reorders all temporary uploads on delete', function () {
    $upload1 = $this->createTemporaryUpload(['collection_name' => 'images', 'custom_properties' => ['priority' => 0]]);
    $upload2 = $this->createTemporaryUpload(['collection_name' => 'images', 'custom_properties' => ['priority' => 1]]);
    $upload3 = $this->createTemporaryUpload(['collection_name' => 'videos', 'custom_properties' => ['priority' => 2]]);

    $response = $this->followingRedirects()->delete(
        route(config('media-library-extensions.route_prefix') . '-temporary-upload-destroy', $upload2),
        []
    );

    $response->assertStatus(200);

    $this->assertDatabaseHas('mle_temporary_uploads', ['id' => $upload1->id]);
    $this->assertDatabaseMissing('mle_temporary_uploads', ['id' => $upload2->id]);
    $this->assertDatabaseHas('mle_temporary_uploads', ['id' => $upload3->id]);

    $upload1->refresh();
    $upload3->refresh();

    expect($upload1->getCustomProperty('priority'))->toBe(0);
    expect($upload3->getCustomProperty('priority'))->toBe(2);
})->todo();
