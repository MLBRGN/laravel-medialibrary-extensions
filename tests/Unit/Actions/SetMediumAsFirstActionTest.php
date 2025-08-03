<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Mlbrgn\MediaLibraryExtensions\Actions\SetMediumAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;

beforeEach(function () {
    // Mock the MediaService dependency
//    $this->mediaService = Mockery::mock(MediaService::class);

    // Instantiate the action with the mocked mediaService
//    $this->action = new SetMediumAsFirstAction($this->mediaService);
});


it('returns error when no media in collection', function () {
    $initiatorId = 'initiator-123';
    $targetCollection = 'images';
//
    $model = $this->getTestModel();


//    // Create request object
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

    expect($response)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);

    $flashKey = config('media-library-extensions.status_session_prefix');
    $flashData = $response->getSession()->get($flashKey);

    expect($flashData)->not()->toBeNull();
    expect($flashData)->toMatchArray([
        'initiatorId' => $initiatorId,
        'type' => 'error',
        'message' => __('media-library-extensions::messages.no_media'),
    ]);

});


it('2 returns error when no media in collection', function () {

    $initiatorId = 'initiator-123';
    $targetCollection = 'images';

    $model = $this->getTestModel();
    $testJpg = $this->getTestFile('test.jpg');

//    dd('test');
//    $fakeUploadedFile1 = new UploadedFile(
//        $testJpg,
//        'photo1.jpg',
//        'image/jpeg',
//        null,
//        true // Mark test mode to skip is_uploaded_file() check
//    );

    $media1 = $model->addMedia($testJpg)
        ->preservingOriginal()
    ->toMediaCollection('blog-images');

//    $model->addMedia($fakeUploadedFile1)->toMediaCollection('blog-images');
//
//    expect($media1)->not->toBeNull();

//    // Create request object
//    $request = new SetAsFirstRequest([
//        'model_type' => $model->getMorphClass(),
//        'model_id' => $model->id,
//        'initiator_id' => $initiatorId,
//        'target_media_collection' => $targetCollection,
//        'medium_id' => 1 //$media1->id,
//    ]);
//
//    $mediaService = new MediaService();
//    $action = new SetMediumAsFirstAction($mediaService);
////    // Call the action's execute method
//    $response = $action->execute($request);
////
//
//    expect($response)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);
//
////    dd($response);
//
//    $flashKey = config('media-library-extensions.status_session_prefix');
//    $flashData = $response->getSession()->get($flashKey);
//
//    expect($flashData)->not()->toBeNull();
//    expect($flashData)->toMatchArray([
//        'initiatorId' => $initiatorId,
//        'type' => 'error',
//        'message' => __('media-library-extensions::messages.no_media'),
//    ]);
//    expect($response)->toBeInstanceOf(JsonResponse::class);
//
//    $data = $response->getData(true);
//
//    expect($data)->toMatchArray([
//        'initiatorId' => $initiatorId,
//        'type' => 'success',
//        'message' => __('media-library-extensions::messages.medium_set_as_main'),
//    ]);

})->skip();
//
//it('sets the given media as first in collection and returns JSON success response', function () {
//    Storage::fake('public');
//
//    $model = Blog::create(['title' => 'Test Article']);
//    $path1 = sys_get_temp_dir() . '/photo1.jpg';
//
//// Create a tiny 1x1 black pixel JPEG image
//    $image = imagecreatetruecolor(1, 1);
//    imagejpeg($image, $path1);
//    imagedestroy($image);
//
//    $fakeUploadedFile1 = new \Illuminate\Http\UploadedFile(
//        $path1,
//        'photo1.jpg',
//        'image/jpeg',
//        null,
//        true
//    );
//
//    $media1 = $model->addMedia($fakeUploadedFile1)
//        ->toMediaCollection('images', 'public');
//
////    expect($media1)->not->toBeNull();
//    // Add fake media so getMedia() returns real media objects
////    $media1 = $model->addMedia($fakeUploadedFile1)
////        ->toMediaCollection('images', 'public');
////    $media2 = $model->addMedia($fakeUploadedFile2)
////        ->toMediaCollection('images', 'public');
////
////    $initiatorId = 'initiator-123';
////    $targetCollection = 'images';
////    $mediumId = $media1->id;
////
////    // Mock mediaService->resolveModel() to return our Blog model
////    $this->mediaService
////        ->shouldReceive('resolveModel')
////        ->once()
////        ->with(get_class($model), $model->id)
////        ->andReturn($model);
////
////    // Mock Media::setNewOrder static call
////    Mockery::mock('alias:' . Media::class)
////        ->shouldReceive('setNewOrder')
////        ->once()
////        ->with([$mediumId, $media2->id]);
////
////    // Create request object
////    $request = new SetAsFirstRequest([
////        'model_type' => get_class($model),
////        'model_id' => $model->id,
////        'initiator_id' => $initiatorId,
////        'target_media_collection' => $targetCollection,
////        'medium_id' => $mediumId,
////    ]);
////
////    // Call the action's execute method
////    $response = $this->action->execute($request);
////
////    expect($response)->toBeInstanceOf(JsonResponse::class);
////
////    $data = $response->getData(true);
////
////    expect($data)->toMatchArray([
////        'initiatorId' => $initiatorId,
////        'type' => 'success',
////        'message' => __('media-library-extensions::messages.medium_set_as_main'),
////    ]);
//})->only();
