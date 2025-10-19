<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\Traits;

it('checks media limits for permanent files', function () {
    //    $file = UploadedFile::fake()->image('photo.jpg');
    $model = $this->getMediaModelWithMedia(['video' => 1]);
    dd($model);
    //    $initiatorId = 'initiator-456';
    //    $mediaManagerId = 'media-manager-123';

    //    $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');

    //    $model->count
    //    $request = StoreSingleRequest::create('/upload', 'POST', [
    //        'model_type' => $model->getMorphClass(),
    //        'model_id' => $model->getKey(),
    //        'initiator_id' => $initiatorId,
    //        'media_manager_id' => $mediaManagerId,
    //        'collections' => ['video' => 'video_collection'],
    //    ], [], [
    //        $uploadFieldNameSingle => $file,
    //    ]);
    //    $request->headers->set('Accept', 'application/json');
    //    $response = (new StoreSinglePermanentAction(new MediaService()))->execute($request);

    //    expect($response)->toBeInstanceOf(Illuminate\Http\JsonResponse::class)
    //        ->and($response->getData(true))
    //        ->toMatchArray([
    //            'initiatorId' => $initiatorId,
    //            'type' => 'error',
    //            'message' => __('media-library-extensions::messages.only_one_medium_allowed'),
    //        ]);
})->todo();

it('checks media limits for temporary files', function () {
    //    $file = UploadedFile::fake()->image('photo.jpg');
    $model = $this->getMediaModelWithMedia(['video' => 1]);
    dd($model);
    //    $initiatorId = 'initiator-456';
    //    $mediaManagerId = 'media-manager-123';

    //    $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');

    //    $model->count
    //    $request = StoreSingleRequest::create('/upload', 'POST', [
    //        'model_type' => $model->getMorphClass(),
    //        'model_id' => $model->getKey(),
    //        'initiator_id' => $initiatorId,
    //        'media_manager_id' => $mediaManagerId,
    //        'collections' => ['video' => 'video_collection'],
    //    ], [], [
    //        $uploadFieldNameSingle => $file,
    //    ]);
    //    $request->headers->set('Accept', 'application/json');
    //    $response = (new StoreSinglePermanentAction(new MediaService()))->execute($request);

    //    expect($response)->toBeInstanceOf(Illuminate\Http\JsonResponse::class)
    //        ->and($response->getData(true))
    //        ->toMatchArray([
    //            'initiatorId' => $initiatorId,
    //            'type' => 'error',
    //            'message' => __('media-library-extensions::messages.only_one_medium_allowed'),
    //        ]);
})->todo();
