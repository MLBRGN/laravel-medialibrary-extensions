<?php

use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\SetMediumAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetTemporaryUploadAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryUploadAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

it('returns error when no media in collection', function () {
    $initiatorId = 'initiator-123';
    $targetCollection = 'images';

//    $media1 = TemporaryUpload::create([
//        'disk' => 'media',
//        'path' => 'uploads/skip1.jpg',
//        'file_name' => 'skip.jpg',
//        'collection_name' => 'test',
//        'custom_properties' => [], // no collection info
//        'session_id' => session()->getId(),
//    ]);
//    $media2 = TemporaryUpload::create([
//        'disk' => 'media',
//        'path' => 'uploads/skip2.jpg',
//        'file_name' => 'skip.jpg',
//        'collection_name' => 'test',
//        'custom_properties' => [], // no collection info
//        'session_id' => session()->getId(),
//    ]);

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
            'initiatorId' => $initiatorId,
            'type' => 'error',
            'message' => __('media-library-extensions::messages.no_media'),
        ]);

});

it('can set as first in collection', function () {

    $initiatorId = 'initiator-123';
    $targetCollection = 'blog-images';

    $media1 = TemporaryUpload::create([
        'disk' => 'media',
        'path' => 'uploads/skip1.jpg',
        'file_name' => 'skip.jpg',
        'collection_name' => $targetCollection,
        'custom_properties' => [], // no collection info
        'session_id' => session()->getId(),
    ]);

    $media2 = TemporaryUpload::create([
        'disk' => 'media',
        'path' => 'uploads/skip2.jpg',
        'file_name' => 'skip.jpg',
        'collection_name' => $targetCollection,
        'custom_properties' => [], // no collection info
        'session_id' => session()->getId(),
    ]);

    // Create request object
    $request = new SetTemporaryUploadAsFirstRequest([
        'initiator_id' => $initiatorId,
        'target_media_collection' => $targetCollection,
        'medium_id' => $media1->id,
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

})->todo('session issues');
