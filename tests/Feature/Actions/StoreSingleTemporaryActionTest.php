<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSingleTemporaryAction;
use Mlbrgn\MediaLibraryExtensions\Exceptions\UploadException;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\UploadPreparerService;

beforeEach(function () {
    Storage::fake(config('medialibrary-extensions.media_disks.temporary'));

    $this->initiatorId = 'initiator-456';
    $this->mediaManagerId = 'media-manager-123';
    $this->model = $this->getTestBlogModel();
    $this->model->save();

    $this->uploadFieldNameSingle =
        config('medialibrary-extensions.upload_field_name');
});

it('stores file and returns JSON success', function () {

    $file = UploadedFile::fake()->image('photo1.jpg');

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id ?? 1,
        'initiator_id' => $this->initiatorId,
        'media_manager_id' => $this->mediaManagerId,
        'collections' => ['image' => 'images'],
        'data_source' => 'demo',
    ], [], [
        'media' => $file,
    ]);

    $mediaService = app(MediaService::class);
    $uploadPreparer = new UploadPreparerService($mediaService);
    $action = new StoreSingleTemporaryAction($mediaService, $uploadPreparer);

    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'application/json');

    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->getData(true))
        ->toMatchArray([
            'initiatorId' => $this->initiatorId,
            'type' => 'success',
            'message' => __('medialibrary-extensions::messages.upload_success'),
        ]);
});

it('stores file and returns redirect success', function () {
    $file = UploadedFile::fake()->image('photo.jpg');

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id ?? 1,
        'initiator_id' => $this->initiatorId,
        'media_manager_id' => $this->mediaManagerId,
        'collections' => ['image' => 'images'],
    ], [], [
        'media' => $file,
    ]);

    $mediaService = app(MediaService::class);
    $uploadPreparer = new UploadPreparerService($mediaService);
    $action = new StoreSingleTemporaryAction($mediaService, $uploadPreparer);

    $request->setLaravelSession(app('session.store'));

    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);
    $session = $request->session();

    expect($session->has(status_session_prefix()))->toBeTrue();

    $sessionData = $session->get(status_session_prefix());

    expect($sessionData['type'])->toBe('success');
    expect($sessionData['initiator_id'])->toBe($this->initiatorId);
    expect($sessionData['message'])->toBe(__('medialibrary-extensions::messages.upload_success'));
});

it('returns error if no file is given (JSON)', function () {

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id ?? 1,
        'initiator_id' => $this->initiatorId,
        'media_manager_id' => $this->mediaManagerId,
        'collections' => ['image' => 'images'],
    ]);
    $request->headers->set('Accept', 'application/json');

    $mediaService = app(MediaService::class);
    $uploadPreparer = new UploadPreparerService($mediaService);
    $action = new StoreSingleTemporaryAction($mediaService, $uploadPreparer);

    //    $response = $action->execute($request);

    expect(fn () => $action->execute($request))->toThrow(UploadException::class);
    //    dd($response->getData(true));
    //    expect($response)->toBeInstanceOf(Illuminate\Http\JsonResponse::class)
    //        ->and($response->getData(true))
    //        ->toMatchArray([
    //            'initiatorId' => $this->initiatorId,
    //            'type' => 'error',
    //            'message' => __('medialibrary-extensions::messages.upload_no_files'),
    //        ]);
});

it('returns error if no file is given (redirect)', function () {
    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id ?? 1,
        'initiator_id' => $this->initiatorId,
        'media_manager_id' => $this->mediaManagerId,
        'collections' => ['image' => 'images'],
    ]);

    $request->setLaravelSession(app('session.store'));

    $mediaService = app(MediaService::class);
    $uploadPreparer = new UploadPreparerService($mediaService);
    $action = new StoreSingleTemporaryAction($mediaService, $uploadPreparer);

    expect(fn () => $action->execute($request))->toThrow(UploadException::class);
});

it('returns error if file has invalid mimetype (JSON)', function () {
    $file = UploadedFile::fake()->create('file.exe', 100, 'application/octet-stream');

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id ?? 1,
        'initiator_id' => $this->initiatorId,
        'media_manager_id' => $this->mediaManagerId,
        'collections' => ['image' => 'images'],
    ], [], [
        'media' => $file,
    ]);

    $request->headers->set('Accept', 'application/json');

    $mediaService = app(MediaService::class);
    $uploadPreparer = new UploadPreparerService($mediaService);
    $action = new StoreSingleTemporaryAction($mediaService, $uploadPreparer);

    expect(fn () => $action->execute($request))->toThrow(UploadException::class);
});

it('returns error if file has invalid mimetype (redirect)', function () {
    $file = UploadedFile::fake()->create('file.exe', 100, 'application/octet-stream');

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id ?? 1,
        'initiator_id' => $this->initiatorId,
        'media_manager_id' => $this->mediaManagerId,
        'collections' => ['image' => 'images'],
    ], [], [
        'media' => $file,
    ]);

    $request->setLaravelSession(app('session.store'));

    $mediaService = app(MediaService::class);
    $uploadPreparer = new UploadPreparerService($mediaService);
    $action = new StoreSingleTemporaryAction($mediaService, $uploadPreparer);

    expect(fn () => $action->execute($request))->toThrow(UploadException::class);
});

it('returns error if file exceeds max upload size (JSON)', function () {
    $model = $this->getTestBlogModel();
    $model->save(); // must be persisted for media attachment

    // Configure: allow normal collection limit but small max file size
    Config::set('medialibrary-extensions.route_middleware', []);
    Config::set('medialibrary-extensions.max_upload_size', 1024 * 100); // 100 KB

    // Create a fake image that exceeds that size (Laravel adds ~1KB per "KB" argument)
    $tooLargeFile = UploadedFile::fake()->create('too_large.jpg', 500); // ~500 KB

    $response = $this
        ->withoutMiddleware(Authenticate::class)
        ->postJson(
            route(config('medialibrary-extensions.route_prefix').'-media-upload-single'),
            [
                'model_type' => $model->getMorphClass(),
                'model_id' => $model->id,
                'initiator_id' => $this->initiatorId,
                'media_manager_id' => $this->mediaManagerId,
                'collections' => ['image' => 'images'],
                'temporary_upload_mode' => 'true',
                'media' => $tooLargeFile,
            ]
        );

    $response->assertStatus(422); // Validation failed

    $responseData = $response->json();

    expect($responseData['message'])->toContain('must not be greater than 100 kilobytes');
});

it('returns error if file exceeds max upload size (redirect)', function () {
    $model = $this->getTestBlogModel();
    $model->save(); // must be persisted for media attachment

    // Configure: allow normal collection limit but small max file size
    Config::set('medialibrary-extensions.route_middleware', []);
    Config::set('medialibrary-extensions.max_upload_size', 1024 * 100); // 100 KB

    // Create a fake image that exceeds that size (Laravel adds ~1KB per "KB" argument)
    $tooLargeFile = UploadedFile::fake()->create('too_large.jpg', 500); // ~500 KB

    $response = $this
        ->withoutMiddleware(Authenticate::class)
        ->post(
            route(config('medialibrary-extensions.route_prefix').'-media-upload-single'),
            [
                'model_type' => $model->getMorphClass(),
                'model_id' => $model->id,
                'initiator_id' => $this->initiatorId,
                'media_manager_id' => $this->mediaManagerId,
                'collections' => ['image' => 'images'],
                'temporary_upload_mode' => 'true',
                'media' => $tooLargeFile,
            ]
        );

    $response->assertStatus(302);

    $response->assertSessionHas('laravel-medialibrary-extensions.status.message', function ($message) {
        return str_contains($message, 'must not be greater than 100 kilobytes');
    });
})->todo('fix this test');
