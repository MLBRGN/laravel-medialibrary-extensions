<?php

use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\SetMediumAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

it('returns error when no media in collection', function () {
    $initiatorId = 'initiator-123';
    $targetCollection = 'images';
    $model = $this->getTestModel();

    // Create request object
    $request = new SetAsFirstRequest([
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->id,
        'initiator_id' => $initiatorId,
        'target_media_collection' => $targetCollection,
        'medium_id' => 1 //$media1->id,
    ]);

    $mediaService = new MediaService();
    $action = new SetMediumAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $flashKey = config('media-library-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull()
        ->and($flashData)->toMatchArray([
            'initiatorId' => $initiatorId,
            'type' => 'error',
            'message' => __('media-library-extensions::messages.no_media'),
        ]);

});


it('can set as first in collection', function () {

    $initiatorId = 'initiator-123';
    $targetCollection = 'blog-images';

    $model = $this->getTestModel();
    $testImage = $this->getUploadedFile('test.png');

    $media1 = $model->addMedia($testImage)
    ->toMediaCollection('blog-images');

    expect($media1)->not->toBeNull();

    // Create request object
    $request = new SetAsFirstRequest([
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->id,
        'initiator_id' => $initiatorId,
        'target_media_collection' => $targetCollection,
        'medium_id' => $media1->id,
    ]);

    $mediaService = new MediaService();
    $action = new SetMediumAsFirstAction($mediaService);

    // Call the action's execute method
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $flashKey = config('media-library-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull();
    expect($flashData)->toMatchArray([
        'initiatorId' => $initiatorId,
        'type' => 'success',
        'message' => __('media-library-extensions::messages.medium_set_as_main'),
    ]);
//    expect($response)->toBeInstanceOf(JsonResponse::class);
//
//    $data = $response->getData(true);
//
//    expect($data)->toMatchArray([
//        'initiatorId' => $initiatorId,
//        'type' => 'success',
//        'message' => __('media-library-extensions::messages.medium_set_as_main'),
//    ]);

});
