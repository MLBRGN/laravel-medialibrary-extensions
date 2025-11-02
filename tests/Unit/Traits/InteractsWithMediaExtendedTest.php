<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSingleTemporaryAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

beforeEach(function () {
    Storage::fake('media');
    Session::start();
    Log::spy();
});

it('attaches temporary media on non created model, and stores medium on model create (removes temporary medium)', function () {

    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $fileName = 'test.jpg';
    $uploadedFile = $this->getFixtureUploadedFile($fileName);
    $model = Blog::make(['title' => 'Testing']);
    $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');

    $request = StoreSingleRequest::create('/upload', 'POST',
        [
            'model_type' => $model->getMorphClass(),
            'model_id' => $model->id,
            'initiator_id' => $initiatorId,
            'media_manager_id' => $mediaManagerId,
            'collections' => ['image' => 'images'],
        ], [], [
            $uploadFieldNameSingle => $uploadedFile,
        ]);
    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'application/json');

    $action = new StoreSingleTemporaryAction(new MediaService);
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\JsonResponse::class)
        ->and($response->getData(true))
        ->toMatchArray([
            'initiatorId' => $initiatorId,
            'type' => 'success',
            'message' => __('media-library-extensions::messages.upload_success'),
        ]);

    $temporaryMedium = TemporaryUpload::forCurrentSession('images');

    expect($temporaryMedium)->not()->toBeNull()
        ->and(TemporaryUpload::count())->toBe(1);

    $model->save();

    $permanentMedium = $model->getMedia('images')->first();

    expect($permanentMedium)->not->toBeNull()
        ->and(Storage::disk('media')->exists('uploads/'.$fileName))->toBeFalse()
        ->and(TemporaryUpload::count())->toBe(0);
});

it('returns error when no collection provided', function () {

    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $fileName = 'test.jpg';
    $uploadedFile = $this->getFixtureUploadedFile($fileName);
    $model = Blog::make(['title' => 'Testing']);
    $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');

    $request = StoreSingleRequest::create('/upload', 'POST',
        [
            'model_type' => $model->getMorphClass(),
            'model_id' => $model->id,
            'initiator_id' => $initiatorId,
            'media_manager_id' => $mediaManagerId,
            //            'collections' => ['image' => 'images'],
        ], [], [
            $uploadFieldNameSingle => $uploadedFile,
        ]);
    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'application/json');

    $action = new StoreSingleTemporaryAction(new MediaService);
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\JsonResponse::class)
        ->and($response->getData(true))
        ->toMatchArray([
            'initiatorId' => $initiatorId,
            'type' => 'error',
            'message' => __('media-library-extensions::messages.no_media_collections'),
        ]);

    $temporaryMedium = TemporaryUpload::forCurrentSession('images');

    expect($temporaryMedium)->not()->toBeNull()
        ->and(TemporaryUpload::count())->toBe(0);

    $model->save();

    $permanentMedium = $model->getMedia('images')->first();

    expect($permanentMedium)->toBeNull();
});

it('logs error when safeAddMedia fails', function () {
    $model = Mockery::mock(Blog::class)->makePartial();
    $model->shouldReceive('addMediaFromDisk')->andThrow(new Exception('Test failure'));

    $refMethod = new ReflectionMethod($model, 'safeAddMedia');
    $refMethod->setAccessible(true);

    $refMethod->invoke($model, $model, 'fake-path.jpg', 'media', 'file.jpg', 'images');

    Log::shouldHaveReceived('error')
        ->withArgs(fn ($message, $context) => str_contains($message, 'Failed to attach media') &&
            $context['path'] === 'fake-path.jpg'
        );
});

it('replaces temporary urls in html editor fields', function () {
    // Arrange: create a temporary upload and fake its storage file
    $upload = TemporaryUpload::newFactory()->create([
        'disk' => 'media',
        'path' => 'uploads/temp.jpg',
        'collection_name' => 'images',
        'session_id' => session()->getId(),
    ]);

    Storage::disk('media')->put('uploads/temp.jpg', 'dummy content');

    // Sanity check — ensure temporary file exists
    expect(Storage::disk('media')->exists('uploads/temp.jpg'))->toBeTrue();

    // The model should support automatic replacement of temporary URLs
    $model = new Blog(['title' => 'Editor']);
    $model->htmlEditorFields = ['content'];

    // The "content" field contains an image referencing the temporary upload URL
    $model->content = '<p><img src="'.$upload->getUrl().'"></p>';

    // Act: save the model — this should trigger conversion from temporary → permanent
    $model->save();
    $model->refresh();

    // Assert: the original temporary URL should be gone
    expect($model->content)
        ->not->toContain($upload->getUrl())
        ->and($model->getMedia('images')->count())->toBe(1);

    $media = $model->getFirstMedia('images');

    expect($media)->not->toBeNull()
        ->and($model->content)->toContain($media->getUrl())
        ->and(TemporaryUpload::count())->toBe(0)
        ->and(Storage::disk('media')->exists($media->getPath()))->toBeTrue();
})->todo();

it('logs info when model does not yet exist', function () {
    Log::spy();

    $model = Blog::make(); // unsaved, no ID

    // Ensure the trait is booted
    if (method_exists(Blog::class, 'bootInteractsWithMediaExtended')) {
        Blog::bootInteractsWithMediaExtended();
    }

    // Get the "created" event listeners for the Blog model
    $listeners = Blog::getEventDispatcher()->getListeners('eloquent.created: '.Blog::class);

    // Call each listener manually with correct payload structure
    foreach ($listeners as $listener) {
        // Pass array payload: [$model]
        $listener('eloquent.created: '.Blog::class, [$model]);
    }

    Log::shouldHaveReceived('info')
        ->withArgs(fn ($msg) => str_contains($msg, 'does not exist'))
        ->atLeast()->once();
});

// it('calculates proper aspect ratio conversions', function () {
//    $media = Mockery::mock(Spatie\MediaLibrary\MediaCollections\Models\Media::class);
//    $media->shouldReceive('getPath')->andReturn(__DIR__.'/fixtures/test-image.jpg');
//
//    $model = new Blog();
//    $model->shouldReceive('addMediaConversion')->with('16x9')->once()->andReturnSelf();
//
//    $model->addResponsive16x9Conversion($media, ['images']);
// });
//
// it('parses conversion names into aspect ratios', function () {
//    $model = new Blog();
//    $model->mediaConversions = collect([
//        (object)['getName' => fn() => '16x9'],
//        (object)['getName' => fn() => '4x3'],
//    ]);
//
//    $result = $model->getRequiredMediaAspectRatio(Mockery::mock(Media::class));
//
//    expect($result)->toMatchArray([
//        '16x9' => 1.7777,
//        '4x3' => 1.3333,
//    ]);
// });
