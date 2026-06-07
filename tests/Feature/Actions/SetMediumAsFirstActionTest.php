<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\SetMediaAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetMediumAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

it('fails when no collections provided JSON', function () {
    $initiatorId = 'initiator-123';
    $mediaManagerId = 'media-manager-123';
    $targetCollection = 'images';
    $model = $this->getTestBlogModel();

    $testImage = $this->getFixtureUploadedFile('test.png');

    $media1 = $model->addMedia($testImage)
        ->toMediaCollection('blog-images');

    expect($media1)->not->toBeNull();

    // Create request object
    $request = new SetMediumAsFirstRequest([
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->id,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'target_media_collection' => $targetCollection,
        'medium_id' => $media1->id,
    ]);
    $request->headers->set('Accept', 'application/json');

    $mediaService = app(MediaService::class);
    $action = new SetMediaAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    $data = $response->getData(true);

    expect($data)->toMatchArray([
        'initiatorId' => $initiatorId,
        //        'media_manager_id' => $mediaManagerId,
        'type' => 'error',
        'message' => __('medialibrary-extensions::messages.no_media_collections'),
    ]);

});

it('fails when no collections provided', function () {
    $initiatorId = 'initiator-123';
    $mediaManagerId = 'media-manager-123';
    $targetCollection = 'images';
    $model = $this->getTestBlogModel();

    $testImage = $this->getFixtureUploadedFile('test.png');

    $media1 = $model->addMedia($testImage)
        ->toMediaCollection('blog-images');

    expect($media1)->not->toBeNull();

    // Create request object
    $request = new SetMediumAsFirstRequest([
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->id,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'target_media_collection' => $targetCollection,
        'medium_id' => $media1->id,
    ]);

    $mediaService = app(MediaService::class);
    $action = new SetMediaAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $flashKey = config('medialibrary-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'initiator_id' => $initiatorId,
            //            'media_manager_id' => $mediaManagerId,
            'type' => 'error',
            'message' => __('medialibrary-extensions::messages.no_media_collections'),
        ]);

});

it('returns error when no media in collection JSON', function () {
    $initiatorId = 'initiator-123';
    $mediaManagerId = 'media-manager-123';
    $targetCollection = 'images';
    $model = $this->getTestBlogModel();

    $testImage = $this->getFixtureUploadedFile('test.png');

    $media1 = $model->addMedia($testImage)
        ->toMediaCollection('blog-images');

    expect($media1)->not->toBeNull();

    // Create request object
    $request = new SetMediumAsFirstRequest([
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->id,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'target_media_collection' => $targetCollection,
        'medium_id' => $media1->id,
        'collections' => ['image' => 'blog-non-existing-collection'],
    ]);
    $request->headers->set('Accept', 'application/json');

    $mediaService = app(MediaService::class);
    $action = new SetMediaAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    $data = $response->getData(true);

    expect($data)->toMatchArray([
        'initiatorId' => $initiatorId,
        'type' => 'error',
        'message' => __('medialibrary-extensions::messages.no_media_collections'),
    ]);

});

it('returns error when no media in collection', function () {
    $initiatorId = 'initiator-123';
    $mediaManagerId = 'media-manager-123';
    $targetCollection = 'images';
    $model = $this->getTestBlogModel();

    $testImage = $this->getFixtureUploadedFile('test.png');

    $media1 = $model->addMedia($testImage)
        ->toMediaCollection('blog-images');

    expect($media1)->not->toBeNull();

    // Create request object
    $request = new SetMediumAsFirstRequest([
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->id,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'target_media_collection' => $targetCollection,
        'medium_id' => $media1->id,
        'collections' => ['image' => 'blog-non-existing-collection'],
    ]);

    $mediaService = app(MediaService::class);
    $action = new SetMediaAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $flashKey = config('medialibrary-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'initiator_id' => $initiatorId,
            'type' => 'error',
            'message' => __('medialibrary-extensions::messages.no_media_collections'),
        ]);

});

it('can set as first in collection JSON', function () {

    $initiatorId = 'initiator-123';
    $mediaManagerId = 'media-manager-123';
    $targetCollection = 'blog-images';

    $model = $this->getTestBlogModel();
    $testImage = $this->getFixtureUploadedFile('test.png');

    $media1 = $model->addMedia($testImage)
        ->toMediaCollection('blog-images');

    expect($media1)->not->toBeNull();

    // Create request object
    $request = new SetMediumAsFirstRequest([
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->id,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'target_media_collection' => $targetCollection,
        'medium_id' => $media1->id,
        'collections' => ['image' => 'blog-images'],
    ]);
    $request->headers->set('Accept', 'application/json');

    $mediaService = app(MediaService::class);
    $action = new SetMediaAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    $data = $response->getData(true);

    expect($data)->toMatchArray([
        'initiatorId' => $initiatorId,
        'type' => 'success',
        'message' => __('medialibrary-extensions::messages.medium_set_as_main'),
    ]);

    $media1->refresh();
    expect($media1->getCustomProperty('priority'))->toBe(0);
    expect($media1->order_column)->toBe(0);
});

it('can set as first in collection', function () {

    $initiatorId = 'initiator-123';
    $mediaManagerId = 'media-manager-123';
    $targetCollection = 'blog-images';

    $model = $this->getTestBlogModel();
    $testImage = $this->getFixtureUploadedFile('test.png');

    $media1 = $model->addMedia($testImage)
        ->toMediaCollection('blog-images');

    expect($media1)->not->toBeNull();

    // Create request object
    $request = new SetMediumAsFirstRequest([
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->id,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'target_media_collection' => $targetCollection,
        'medium_id' => $media1->id,
        'collections' => ['image' => 'blog-images'],
    ]);

    $mediaService = app(MediaService::class);
    $action = new SetMediaAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $flashKey = config('medialibrary-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull();
    expect($flashData)->toMatchArray([
        'initiator_id' => $initiatorId,
        'type' => 'success',
        'message' => __('medialibrary-extensions::messages.medium_set_as_main'),
    ]);

});
