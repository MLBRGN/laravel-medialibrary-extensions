<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mlbrgn\MediaLibraryExtensions\Actions\DestroyMediaAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\ParameterBag;

covers(DestroyMediaAction::class);

it('deletes the medium and returns JSON', function () {
    $user = $this->getUser();
    $baseId = 'initiator-123';
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

    // Call the "destroy" route
    $route = route(mle_prefix_route('destroy-media'), $media);
    $response = $this->actingAs($user)->deleteJson($route, [
        'base_id' => $baseId,
        'collections' => ['image' => 'images'],
        'model_type' => $model->getMorphClass(),
        'model_id' => (string) $model->getKey(),
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'baseId' => $baseId,
            'type' => 'success',
            'message' => __('medialibrary-extensions::messages.medium_removed'),
        ]);

    $this->assertDatabaseMissing('media', ['id' => $media->id]);
});

it('deletes the medium and returns Redirect', function () {
    $user = $this->getUser();
    $baseId = 'initiator-123';
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

    // Call the "destroy" route
    $route = route(mle_prefix_route('destroy-media'), $media);
    $response = $this->actingAs($user)->delete($route, [
        'base_id' => $baseId,
        'collections' => $collections,
        'model_type' => $model->getMorphClass(),
        'model_id' => (string) $model->getKey(),
    ]);

    $response->assertRedirect();

    $flashKey = config('medialibrary-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'base_id' => $baseId,
            'type' => 'success',
            'message' => __('medialibrary-extensions::messages.medium_removed'),
        ]);

    $this->assertDatabaseMissing('media', ['id' => $media->id]);
});

it('reorders all media on delete', function () {
    $user = $this->getUser();
    $baseId = 'initiator-123';
    $collections = ['image' => 'images'];

    // Create the model with multiple media items
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
            'base_id' => $baseId,
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

it('deletes a medium and reorders priorities via action execute (JSON)', function () {
    // Arrange: attach some media
    $model = $this->getTestBlogModel();

    $testImage = $this->getFixtureUploadedFile('test.png');
    $testImage2 = $this->getFixtureUploadedFile('test2.png');
    $first = $model->addMedia($testImage)
        ->preservingOriginal()
        ->withCustomProperties(['priority' => 5])
        ->toMediaCollection('images');

    $second = $model->addMedia($testImage2)
        ->preservingOriginal()
        ->withCustomProperties(['priority' => 1])
        ->toMediaCollection('images');

    $request = DestroyRequest::create('/media/'.$first->id, 'DELETE', [
        'mediaId' => $first->id,
        'base_id' => 'foo',
        'collections' => ['image' => 'images'],
        'model_type' => get_class($model),
        'model_id' => $model->id,
    ]);

    // Simulate an AJAX/JSON request
    $request->headers->set('Accept', 'application/json');
    $request->setJson(new ParameterBag($request->all()));

    $action = app(DestroyMediaAction::class);

    // Act
    $response = $action->execute($request, $first);

    // Assert response
    expect($response->getStatusCode())->toBe(200);
    expect($response->getData(true))
        ->toHaveKey('message')
        ->toMatchArray(['baseId' => 'foo']);

    $mediaService = app(MediaService::class);
    // The deleted medium should be gone
    try {
        $mediaService->resolveModelById(Media::class, $first->id, 'default');
        $this->fail('The medium should have been deleted');
    } catch (ModelNotFoundException $e) {
        // Expected
    }

    // Priorities should be re-ordered starting from 0
    $remaining = $model->getMedia('images');
    expect($remaining)->toHaveCount(1);
    expect($remaining->first()->getCustomProperty('priority'))->toBe(0);
});

it('skips reorder if no collections are passed via action execute', function () {
    $model = $this->getTestBlogModel();

    // Arrange: create a single media item
    $media = $model->addMedia($this->getFixtureUploadedFile('test.png'))
        ->preservingOriginal()
        ->withCustomProperties(['priority' => 99])
        ->toMediaCollection('images');

    // Act: create a request with no collections
    $request = DestroyRequest::create('/media/'.$media->id, 'DELETE', [
        'mediaId' => $media->id,
        'base_id' => 'foo',
    ]);

    // Make sure Laravel treats this as a JSON request
    $request->headers->set('Accept', 'application/json');
    $request->setJson(new ParameterBag($request->all()));

    $action = app(DestroyMediaAction::class);

    // Execute delete action
    $response = $action->execute($request, $media);

    // Assert: medium is deleted
    expect(Media::find($media->id))->toBeNull();

    // Assert: response is JSON 200
    expect($response->getStatusCode())->toBe(200);
    $data = $response->getData(true);
    expect($data)->toHaveKey('message');
    expect($data['baseId'])->toBe('foo');
});
