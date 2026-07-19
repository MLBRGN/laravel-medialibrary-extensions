<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSinglePermanentAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\UploadPreparerService;

beforeEach(function () {
    Storage::fake(config('medialibrary-extensions.media_disks.temporary'));

    $this->baseId = 'initiator-456';
    $this->baseId = 'media-manager-123';

    $this->model = $this->getTestBlogModel();

    $this->uploadFieldNameSingle =
        config('medialibrary-extensions.upload_field_name');

    $this->mediaService = app(MediaService::class);
    $this->uploadPreparer = app(UploadPreparerService::class);

    $this->action = new StoreSinglePermanentAction(
        $this->mediaService,
        $this->uploadPreparer
    );
});

it('stores file (json)', function () {
    $baseId = 'media-manager-123';
    $file1 = UploadedFile::fake()->image('photo1.jpg');
    $model = $this->getTestBlogModel();

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id ?? 1,
        'base_id' => $this->baseId,
        'collections' => ['image' => 'images'],
    ], [], [
        'media' => $file1,
    ]);
    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'application/json');

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->getData(true))
        ->toMatchArray([
            'baseId' => $this->baseId,
            'type' => 'success',
            'message' => __('medialibrary-extensions::messages.upload_success'),
        ]);
});

it('stores file (redirect)', function () {
    $file = UploadedFile::fake()->image('photo.jpg');

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id ?? 1,
        'base_id' => $this->baseId,
        'collections' => ['image' => 'images'],
    ], [], [
        'media' => $file,
    ]);
    $request->setLaravelSession(app('session.store'));

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);
    $session = $request->session();

    expect($session->has(status_session_prefix()))->toBeTrue();

    $sessionData = $session->get(status_session_prefix());

    expect($sessionData['type'])->toBe('success');
    expect($sessionData['base_id'])->toBe($this->baseId);
    expect($sessionData['message'])->toBe(__('medialibrary-extensions::messages.upload_success'));
});

it('returns error if no file is given (JSON)', function () {

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id ?? 1,
        'base_id' => $this->baseId,
        'collections' => ['image' => 'images'],
    ]);
    $request->headers->set('Accept', 'application/json');

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->getData(true))
        ->toMatchArray([
            'baseId' => $this->baseId,
            'type' => 'error',
            'message' => __('medialibrary-extensions::messages.upload_no_files'),
        ]);
});

it('returns error if no file is given (redirect)', function () {
    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id ?? 1,
        'base_id' => $this->baseId,
        'collections' => ['image' => 'images'],
    ]);

    $request->setLaravelSession(app('session.store'));

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $session = $request->session();

    expect($session->has(status_session_prefix()))->toBeTrue();

    $sessionData = $session->get(status_session_prefix());

    expect($sessionData['type'])->toBe('error');
    expect($sessionData['base_id'])->toBe($this->baseId);
    expect($sessionData['message'])->toBe(__('medialibrary-extensions::messages.upload_no_files'));
});

it('returns error if file has invalid mimetype (JSON)', function () {
    $file = UploadedFile::fake()->create('file.exe', 100, 'application/octet-stream');

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id ?? 1,
        'base_id' => $this->baseId,
        'collections' => ['image' => 'images'],
    ], [], [
        'media' => $file,
    ]);
    $request->headers->set('Accept', 'application/json');

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->getData(true)['type'])->toBe('error')
        ->and($response->getData(true)['message'])->toBe(
            __('medialibrary-extensions::messages.upload_failed_due_to_invalid_mimetype')
        );
});

it('returns error if file has invalid mimetype (redirect)', function () {
    $file = UploadedFile::fake()->create('file.exe', 100, 'application/octet-stream');

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id ?? 1,
        'base_id' => $this->baseId,
        'collections' => ['image' => 'images'],
    ], [], [
        'media' => $file,
    ]);
    $request->setLaravelSession(app('session.store'));

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $session = $request->session();

    expect($session->has(status_session_prefix()))->toBeTrue();

    $sessionData = $session->get(status_session_prefix());

    expect($sessionData['type'])->toBe('error');
    expect($sessionData['base_id'])->toBe($this->baseId);
    expect($sessionData['message'])->toBe(__('medialibrary-extensions::messages.upload_failed_due_to_invalid_mimetype'));
});

it('returns error if model already has media in given collections (JSON)', function () {
    $file = UploadedFile::fake()->image('photo.jpg');
    $model = $this->getModelWithMedia([
        'image' => 1,
    ]);

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => $model->id ?? 1,
        'base_id' => $this->baseId,
        'collections' => ['image' => 'image_collection'],
    ], [], [
        'media' => $file,
    ]);
    $request->headers->set('Accept', 'application/json');
    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->getData(true))
        ->toMatchArray([
            'baseId' => $this->baseId,
            'type' => 'error',
            'message' => __('medialibrary-extensions::messages.only_one_medium_allowed'),
        ]);
});

it('returns error if model already has media in given collections (redirect)', function () {
    $file = UploadedFile::fake()->image('photo.jpg');
    $model = $this->getModelWithMedia([
        'image' => 1,
    ]);

    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => $model->id ?? 1,
        'base_id' => $this->baseId,
        'collections' => ['image' => 'image_collection'],
    ], [], [
        'media' => $file,
    ]);

    $request->setLaravelSession(app('session.store'));

    $response = $this->action->execute($request);
    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $session = $request->session();
    expect($session->has(status_session_prefix()))->toBeTrue();

    $sessionData = $session->get(status_session_prefix());

    expect($sessionData)->toMatchArray([
        'type' => 'error',
        'base_id' => $this->baseId,
        'message' => __('medialibrary-extensions::messages.only_one_medium_allowed'),
    ]);
});

it('returns error if file exceeds max upload size (JSON)', function () {
    $model = $this->getTestBlogModel();
    $model->save(); // must be persisted for media attachment

    // Configure: allow the normal collection limit but a small max file size
    // Use 'web' middleware to enable session and redirect behavior for validation errors
    Config::set('medialibrary-extensions.route_middleware', ['web']);
    Config::set('medialibrary-extensions.max_upload_size', 1024 * 100); // 100 KB

    // Create a fake image that exceeds that size (Laravel adds ~1KB per "KB" argument)
    $tooLargeFile = UploadedFile::fake()->create('too_large.jpg', 500); // ~500 KB

    $response = $this
        ->withoutMiddleware(Authenticate::class)
        ->postJson(
            route(config('medialibrary-extensions.route_prefix').'-media-upload-single'),
            [
                'model_type' => $model->getMorphClass(),
                'model_id' => $model->getKey(),
                'base_id' => $this->baseId,
                'collections' => ['image' => 'images'],
                'temporary_upload_mode' => 'false',
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

    // Configure: allow the normal collection limit but a small max file size
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
                'model_id' => $model->getKey(),
                'base_id' => $this->baseId,
                'collections' => ['image' => 'images'],
                'temporary_upload_mode' => 'false',
                'media' => $tooLargeFile,
            ]
        );

    $response->assertStatus(422);
    expect($response->json('message'))
        ->toContain('must not be greater than 100 kilobytes');
});
