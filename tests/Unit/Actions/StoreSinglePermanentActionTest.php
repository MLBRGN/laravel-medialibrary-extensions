<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\UploadedFile;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSinglePermanentAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

beforeEach(function () {
    $this->mediaService = \Mockery::mock(MediaService::class);
    $this->action = new StoreSinglePermanentAction($this->mediaService);
});

it('stores file (json)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $file1 = UploadedFile::fake()->image('photo1.jpg');
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);
    $this->mediaService
        ->shouldReceive('determineCollectionType')->once()->andReturn('image');

    $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');
    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['image' => 'images'],
    ], [], [
        $uploadFieldNameSingle => $file1,
    ]);
    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'application/json');

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\JsonResponse::class)
        ->and($response->getData(true))
        ->toMatchArray([
            'initiatorId' => $initiatorId,
            'type' => 'success',
            'message' => __('media-library-extensions::messages.upload_success'),
        ]);
});

it('stores file and returns redirect success', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $file = UploadedFile::fake()->image('photo.jpg');
    $model = $this->getTestBlogModel();
    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);
    $this->mediaService
        ->shouldReceive('determineCollectionType')->once()->andReturn('image');

    $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');
    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['image' => 'images'],
    ], [], [
        $uploadFieldNameSingle => $file,
    ]);
    $request->setLaravelSession(app('session.store'));

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\RedirectResponse::class);
    $session = $request->session();

    expect($session->has('laravel-medialibrary-extensions.status'))->toBeTrue();

    $sessionData = $session->get('laravel-medialibrary-extensions.status');

    expect($sessionData['type'])->toBe('success');
    expect($sessionData['initiator_id'])->toBe($initiatorId);
    expect($sessionData['message'])->toBe(__('media-library-extensions::messages.upload_success'));
});

it('returns error if no file is given (JSON)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['image' => 'images'],
    ]);
    $request->headers->set('Accept', 'application/json');

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\JsonResponse::class)
        ->and($response->getData(true))
        ->toMatchArray([
            'initiatorId' => $initiatorId,
            'type' => 'error',
            'message' => __('media-library-extensions::messages.upload_no_files'),
        ]);
});

it('returns error if no file is given (redirect)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['image' => 'images'],
    ]);

    $request->setLaravelSession(app('session.store'));

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\RedirectResponse::class);

    $session = $request->session();

    expect($session->has('laravel-medialibrary-extensions.status'))->toBeTrue();

    $sessionData = $session->get('laravel-medialibrary-extensions.status');

    expect($sessionData['type'])->toBe('error');
    expect($sessionData['initiator_id'])->toBe($initiatorId);
    expect($sessionData['message'])->toBe(__('media-library-extensions::messages.upload_no_files'));
});

it('returns error if file has invalid mimetype (JSON)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $file = UploadedFile::fake()->create('file.exe', 100, 'application/octet-stream');
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);
    $this->mediaService
        ->shouldReceive('determineCollectionType')->once()->andReturn(null);

    $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['image' => 'images'],
    ], [], [
        $uploadFieldNameSingle => $file,
    ]);
    $request->headers->set('Accept', 'application/json');

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\JsonResponse::class)
        ->and($response->getData(true)['type'])->toBe('error')
        ->and($response->getData(true)['message'])->toBe(
            __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype')
        );
});

it('returns error if file has invalid mimetype (redirect)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $file = UploadedFile::fake()->create('file.exe', 100, 'application/octet-stream');
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);
    $this->mediaService
        ->shouldReceive('determineCollectionType')->once()->andReturn(null);

    $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['image' => 'images'],
    ], [], [
        $uploadFieldNameSingle => $file,
    ]);
    $request->setLaravelSession(app('session.store'));

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\RedirectResponse::class);

    $session = $request->session();

    expect($session->has('laravel-medialibrary-extensions.status'))->toBeTrue();

    $sessionData = $session->get('laravel-medialibrary-extensions.status');

    expect($sessionData['type'])->toBe('error');
    expect($sessionData['initiator_id'])->toBe($initiatorId);
    expect($sessionData['message'])->toBe(__('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype'));
});

it('returns error if model already has media in given collections (JSON)', function () {
    $file = UploadedFile::fake()->image('photo.jpg');
    $model = $this->getModelWithMedia(['video' => 1]);
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';

    $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->getKey(),
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['video' => 'video_collection'],
    ], [], [
        $uploadFieldNameSingle => $file,
    ]);
    $request->headers->set('Accept', 'application/json');
    $response = (new StoreSinglePermanentAction(new MediaService))->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\JsonResponse::class)
        ->and($response->getData(true))
        ->toMatchArray([
            'initiatorId' => $initiatorId,
            'type' => 'error',
            'message' => __('media-library-extensions::messages.only_one_medium_allowed'),
        ]);
});

it('returns error if model already has media in given collections (redirect)', function () {
    $file = UploadedFile::fake()->image('photo.jpg');
    $model = $this->getModelWithMedia([
        'audio' => 1,
    ]);
    $initiatorId = 'initiator-457';
    $mediaManagerId = 'media-manager-124';

    $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['video' => 'audio_collection'],
    ], [], [
        $uploadFieldNameSingle => $file,
    ]);

    $request->setLaravelSession(app('session.store'));

    $response = (new StoreSinglePermanentAction(new MediaService))->execute($request);
    expect($response)->toBeInstanceOf(Illuminate\Http\RedirectResponse::class);

    $session = $request->session();
    expect($session->has('laravel-medialibrary-extensions.status'))->toBeTrue();

    $sessionData = $session->get('laravel-medialibrary-extensions.status');

    expect($sessionData)->toMatchArray([
        'type' => 'error',
        'initiator_id' => $initiatorId,
        'message' => __('media-library-extensions::messages.only_one_medium_allowed'),
    ]);
});

it('returns error if file exceeds max upload size (JSON)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $model = $this->getTestBlogModel();
    $model->save(); // must be persisted for media attachment

    // Configure: allow normal collection limit but small max file size
    Config::set('media-library-extensions.route_middleware', []);
    Config::set('media-library-extensions.max_upload_size', 1024 * 100); // 100 KB

    // Create a fake image that exceeds that size (Laravel adds ~1KB per "KB" argument)
    $tooLargeFile = UploadedFile::fake()->create('too_large.jpg', 500); // ~500 KB

    $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');

    $response = $this
        ->withoutMiddleware(Authenticate::class)
        ->postJson(
            route(config('media-library-extensions.route_prefix').'-media-upload-single'),
            [
                'model_type' => $model->getMorphClass(),
                'model_id' => $model->getKey(),
                'initiator_id' => $initiatorId,
                'media_manager_id' => $mediaManagerId,
                'collections' => ['image' => 'images'],
                'temporary_upload_mode' => 'false',
                $uploadFieldNameSingle => $tooLargeFile,
            ]
        );

    $response->assertStatus(422); // Validation failed

    $responseData = $response->json();

    expect($responseData['message'])->toContain('exceeds the maximum allowed size');

});

it('returns error if file exceeds max upload size (redirect)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $model = $this->getTestBlogModel();
    $model->save(); // must be persisted for media attachment

    // Configure: allow normal collection limit but small max file size
    Config::set('media-library-extensions.route_middleware', []);
    Config::set('media-library-extensions.max_upload_size', 1024 * 100); // 100 KB

    // Create a fake image that exceeds that size (Laravel adds ~1KB per "KB" argument)
    $tooLargeFile = UploadedFile::fake()->create('too_large.jpg', 500); // ~500 KB

    $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');

    $response = $this
        ->withoutMiddleware(Authenticate::class)
        ->post(
            route(config('media-library-extensions.route_prefix').'-media-upload-single'),
            [
                'model_type' => $model->getMorphClass(),
                'model_id' => $model->getKey(),
                'initiator_id' => $initiatorId,
                'media_manager_id' => $mediaManagerId,
                'collections' => ['image' => 'images'],
                'temporary_upload_mode' => 'false',
                $uploadFieldNameSingle => $tooLargeFile,
            ]
        );

    $response->assertStatus(302);

    $response->assertSessionHas('laravel-medialibrary-extensions.status.message', function ($message) {
        return str_contains($message, 'exceeds the maximum allowed size');
    });

});
