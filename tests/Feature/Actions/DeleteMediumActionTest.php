<?php

use Mlbrgn\MediaLibraryExtensions\Actions\DestroyMediaAction;

covers(DestroyMediaAction::class);

it('deletes the medium and returns JSON', function () {
    $user = $this->getUser();
    $initiatorId = 'initiator-123';
    $mediaManagerDomId = 'media-manager-123';
    $imageCollectionName = 'images';

    // Create a model with media
    $model = $this->getModelWithMedia([
        'image' => 2,
    ]);

    // Attach a medium
    $media = $model->addMedia($this->getTestImagePath())
        ->preservingOriginal()
        ->toMediaCollection($imageCollectionName);

    $this->assertDatabaseHas('media', ['id' => $media->id]);

    // Call the destroy route
    $route = route(mle_prefix_route('destroy-media'), $media);
    $response = $this->actingAs($user)->deleteJson($route, [
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerDomId,
        'collections' => ['image' => 'images'],
        'model_type' => $model->getMorphClass(),
        'model_id' => (string) $model->getKey(),
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'initiatorId' => $initiatorId,
            'type' => 'success',
            'message' => __('medialibrary-extensions::messages.medium_removed'),
        ]);

    $this->assertDatabaseMissing('media', ['id' => $media->id]);
});

it('deletes the medium and returns Redirect', function () {
    $user = $this->getUser();
    $initiatorId = 'initiator-123';
    $mediaManagerDomId = 'media-manager-123';
    $collections = ['image' => 'images'];

    // Create a model with media
    $model = $this->getModelWithMedia([
        'image' => 2,
    ]);

    // Attach a medium
    $media = $model->addMedia($this->getTestImagePath())
        ->preservingOriginal()
        ->toMediaCollection($collections['image']);

    $this->assertDatabaseHas('media', ['id' => $media->id]);

    // Call the destroy route
    $route = route(mle_prefix_route('destroy-media'), $media);
    $response = $this->actingAs($user)->delete($route, [
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerDomId,
        'collections' => $collections,
        'model_type' => $model->getMorphClass(),
        'model_id' => (string) $model->getKey(),
    ]);

    $response->assertRedirect();

    $flashKey = config('medialibrary-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'initiator_id' => $initiatorId,
            'type' => 'success',
            'message' => __('medialibrary-extensions::messages.medium_removed'),
        ]);

    $this->assertDatabaseMissing('media', ['id' => $media->id]);
});

it('reorders all media on delete', function () {
    $user = $this->getUser();
    $initiatorId = 'initiator-123';
    $mediaManagerDomId = 'media-manager-123';
    $collections = ['image' => 'images'];

    // Create model with multiple media items
    $model = $this->getModelWithMedia([
        'image' => 2,
    ]);

    $media1 = $model->addMedia($this->getTestImagePath())
        ->withCustomProperties(['priority' => 0])
        ->toMediaCollection($collections['image']);

    $media2 = $model->addMedia($this->getTestImagePath())
        ->withCustomProperties(['priority' => 1])
        ->toMediaCollection($collections['image']);

    $media3 = $model->addMedia($this->getTestImagePath())
        ->withCustomProperties(['priority' => 2])
        ->toMediaCollection($collections['image']);

    $route = route(mle_prefix_route('destroy-media'), $media2);

    // Delete second medium
    $response = $this
        ->actingAs($user)
        ->delete($route, [
            'initiator_id' => $initiatorId,
            'media_manager_id' => $mediaManagerDomId,
            'collections' => $collections,
            'model_type' => $model->getMorphClass(),
            'model_id' => (string) $model->getKey(),
        ]);

    $response->assertRedirect();

    // Refresh media
    $media1->refresh();
    $media3->refresh();

    // Check priorities were reassigned
    expect($media1->getCustomProperty('priority'))->toBe(0);
    expect($media3->getCustomProperty('priority'))->toBe(1);

    $this->assertDatabaseMissing('media', ['id' => $media2->id]);
});
