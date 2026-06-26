<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultipleTemporaryAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

it('stores multiple valid files (json)', function () {
    $baseId = 'initiator-456';
    $baseId = 'media-manager-123';
    $file1 = UploadedFile::fake()->image('photo1.jpg');
    $file2 = UploadedFile::fake()->image('photo2.jpg');
    $model = $this->getTestBlogModel();

    $request = StoreMultipleRequest::create('/upload', 'POST', [
        'temporary_upload_mode' => 'true',
        'model_type' => get_class($model),
        'model_id' => 1,
        'base_id' => $baseId,
        'base_id' => $baseId,
        'collections' => ['image' => 'images'],
        'data_source' => 'default',
    ], [], [
        'media' => [$file1, $file2],
    ]);
    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'application/json');

    $mediaService = app(MediaService::class);
    $action = new StoreMultipleTemporaryAction($mediaService);
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->getData(true))
        ->toMatchArray([
            'baseId' => $baseId,
            'type' => 'success',
            'message' => __('medialibrary-extensions::messages.upload_success'),
        ]);
});

it('stores multiple valid files (redirect)', function () {
    $baseId = 'initiator-456';
    $baseId = 'media-manager-123';
    $file = UploadedFile::fake()->image('photo.jpg');
    $model = $this->getTestBlogModel();

    $request = StoreMultipleRequest::create('/upload', 'POST', [
        'temporary_upload_mode' => 'true',
        'model_type' => get_class($model),
        'model_id' => 1,
        'base_id' => $baseId,
        'base_id' => $baseId,
        'collections' => ['image' => 'images'],
        'data_source' => 'default',
    ], [], [
        'media' => [$file],
    ]);
    $request->setLaravelSession(app('session.store'));

    $mediaService = app(MediaService::class);
    $action = new StoreMultipleTemporaryAction($mediaService);
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);
    $session = $request->session();

    expect($session->has(status_session_prefix()))->toBeTrue();

    $sessionData = $session->get(status_session_prefix());

    expect($sessionData['type'])->toBe('success');
    expect($sessionData['base_id'])->toBe($baseId);
    expect($sessionData['message'])->toBe(__('medialibrary-extensions::messages.upload_success'));
});

it('returns error if no files are given (JSON)', function () {
    $baseId = 'initiator-456';
    $baseId = 'media-manager-123';
    $model = $this->getTestBlogModel();

    $request = StoreMultipleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'base_id' => $baseId,
        'base_id' => $baseId,
        'collections' => ['image' => 'images'],
        'data_source' => 'default',
    ]);
    $request->headers->set('Accept', 'application/json');
    $request->setLaravelSession(app('session.store'));

    $mediaService = app(MediaService::class);
    $action = new StoreMultipleTemporaryAction($mediaService);
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->getData(true))
        ->toMatchArray([
            'baseId' => $baseId,
            'type' => 'error',
            'message' => __('medialibrary-extensions::messages.upload_no_files'),
        ]);
});

it('returns error if no files are given (redirect)', function () {
    $baseId = 'initiator-456';
    $baseId = 'media-manager-123';
    $model = $this->getTestBlogModel();

    $request = StoreMultipleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'base_id' => $baseId,
        'base_id' => $baseId,
        'collections' => ['image' => 'images'],
        'data_source' => 'default',
    ]);

    $request->setLaravelSession(app('session.store'));

    $mediaService = app(MediaService::class);
    $action = new StoreMultipleTemporaryAction($mediaService);
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $session = $request->session();

    expect($session->has(status_session_prefix()))->toBeTrue();

    $sessionData = $session->get(status_session_prefix());

    expect($sessionData['type'])->toBe('error');
    expect($sessionData['base_id'])->toBe($baseId);
    expect($sessionData['message'])->toBe(__('medialibrary-extensions::messages.upload_no_files'));
});

it('returns error if file has invalid mimetype (JSON)', function () {
    $baseId = 'initiator-456';
    $baseId = 'media-manager-123';
    $file = UploadedFile::fake()->create('file.exe', 100, 'application/octet-stream');
    $model = $this->getTestBlogModel();

    $request = StoreMultipleRequest::create('/upload', 'POST', [
        'temporary_upload_mode' => 'true',
        'model_type' => get_class($model),
        'model_id' => 1,
        'base_id' => $baseId,
        'base_id' => $baseId,
        'collections' => ['image' => 'images'],
        'data_source' => 'default',
    ], [], [
        'media' => [$file],
    ]);
    $request->headers->set('Accept', 'application/json');
    $request->setLaravelSession(app('session.store'));

    $mediaService = app(MediaService::class);
    $action = new StoreMultipleTemporaryAction($mediaService);
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->getData(true)['type'])->toBe('error')
        ->and($response->getData(true)['message'])->toContain(
            __('medialibrary-extensions::messages.invalid_or_missing_collection', ['file' => 'file.exe'])
        );
});

it('returns error if file has invalid mimetype (redirect)', function () {
    $baseId = 'initiator-456';
    $baseId = 'media-manager-123';
    $file = UploadedFile::fake()->create('file.exe', 100, 'application/octet-stream');
    $model = $this->getTestBlogModel();

    $request = StoreMultipleRequest::create('/upload', 'POST', [
        'temporary_upload_mode' => 'true',
        'model_type' => get_class($model),
        'model_id' => 1,
        'base_id' => $baseId,
        'base_id' => $baseId,
        'collections' => ['image' => 'images'],
        'data_source' => 'default',
    ], [], [
        'media' => [$file],
    ]);
    $request->setLaravelSession(app('session.store'));

    $mediaService = app(MediaService::class);
    $action = new StoreMultipleTemporaryAction($mediaService);
    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $session = $request->session();

    expect($session->has(status_session_prefix()))->toBeTrue();

    $sessionData = $session->get(status_session_prefix());

    expect($sessionData['type'])->toBe('error');
    expect($sessionData['base_id'])->toBe($baseId);
    expect($sessionData['message'])->toContain(__('medialibrary-extensions::messages.invalid_or_missing_collection', ['file' => 'file.exe']));
});

it('returns error if max media count is exceeded (JSON)', function () {
    $baseId = 'initiator-456';
    $baseId = 'media-manager-123';
    $model = $this->getTestBlogModel();
    $model->save(); // must be persisted for media attachment

    // Attach 1 existing media item
    $model->addMedia(UploadedFile::fake()->image('existing.jpg'))
        ->toMediaCollection('images');

    // Max = 2, but existing = 1 and adding 4 new => total = 5 > 2
    Config::set('medialibrary-extensions.route_middleware', []);
    Config::set('medialibrary-extensions.max_items_in_shared_media_collections', 2);

    $file1 = UploadedFile::fake()->image('photo1.jpg');
    $file2 = UploadedFile::fake()->image('photo2.jpg');
    $file3 = UploadedFile::fake()->image('photo3.jpg');
    $file4 = UploadedFile::fake()->image('photo4.jpg');

    $response = $this->withoutMiddleware(Authenticate::class)->postJson(
        route(config('medialibrary-extensions.route_prefix').'-media-upload-multiple'),
        [
            'model_type' => $model->getMorphClass(),
            'model_id' => $model->id,
            'base_id' => $baseId,
            'base_id' => $baseId,
            'collections' => ['image' => 'images'],
            'temporary_upload_mode' => 'true',
            'media' => [$file1, $file2, $file3, $file4],
        ]
    );

    $response->assertStatus(422); // Validation failed
    $response->assertJsonValidationErrors(['media']); // or your field name

    $responseData = $response->json();

    expect($responseData['message'])->toBe(__('medialibrary-extensions::messages.this_collection_can_contain_up_to_:items_items', ['items' => config('medialibrary-extensions.max_items_in_shared_media_collections')]));
    expect($responseData['errors']['media'][0])->toBe(__('medialibrary-extensions::messages.this_collection_can_contain_up_to_:items_items', ['items' => config('medialibrary-extensions.max_items_in_shared_media_collections')]));
});

it('returns error if max media count is exceeded (redirect)', function () {
    $baseId = 'initiator-456';
    $baseId = 'media-manager-123';
    $model = $this->getTestBlogModel();
    $model->save(); // must be persisted for media attachment

    // Attach 1 existing media item
    $model->addMedia(UploadedFile::fake()->image('existing.jpg'))
        ->toMediaCollection('images');

    // Max = 2, but existing = 1 and adding 4 new => total = 5 > 2
    Config::set('medialibrary-extensions.route_middleware', []);
    Config::set('medialibrary-extensions.max_items_in_shared_media_collections', 2);

    $file1 = UploadedFile::fake()->image('photo1.jpg');
    $file2 = UploadedFile::fake()->image('photo2.jpg');
    $file3 = UploadedFile::fake()->image('photo3.jpg');
    $file4 = UploadedFile::fake()->image('photo4.jpg');

    $response = $this->withoutMiddleware(Authenticate::class)->post(
        route(config('medialibrary-extensions.route_prefix').'-media-upload-multiple'),
        [
            'model_type' => $model->getMorphClass(),
            'model_id' => $model->id,
            'base_id' => $baseId,
            'base_id' => $baseId,
            'collections' => ['image' => 'images'],
            'temporary_upload_mode' => 'true',
            'media' => [$file1, $file2, $file3, $file4],
        ]
    );

    $response->assertStatus(302);

    // Assert validation error is flashed to session
    $response->assertSessionHasErrors([
        'media' => __('medialibrary-extensions::messages.this_collection_can_contain_up_to_:items_items', ['items' => config('medialibrary-extensions.max_items_in_shared_media_collections')]),
    ]);
});

it('returns error if file exceeds max upload size (JSON)', function () {
    $baseId = 'initiator-456';
    $baseId = 'media-manager-123';
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
            route(config('medialibrary-extensions.route_prefix').'-media-upload-multiple'),
            [
                'model_type' => $model->getMorphClass(),
                'model_id' => $model->id,
                'base_id' => $baseId,
                'base_id' => $baseId,
                'collections' => ['image' => 'images'],
                'temporary_upload_mode' => 'true',
                'media' => [$tooLargeFile],
                'data_source' => 'default',
            ]
        );

    $response->assertStatus(422); // Validation failed

    $responseData = $response->json();

    expect($responseData['message'])->toContain('must not be greater than 100 kilobytes');
});

it('returns error if file exceeds max upload size (redirect)', function () {
    $baseId = 'initiator-456';
    $baseId = 'media-manager-123';
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
            route(config('medialibrary-extensions.route_prefix').'-media-upload-multiple'),
            [
                'model_type' => $model->getMorphClass(),
                'model_id' => $model->id,
                'base_id' => $baseId,
                'base_id' => $baseId,
                'collections' => ['image' => 'images'],
                'temporary_upload_mode' => 'true',
                'media' => [$tooLargeFile],
                'data_source' => 'default',
            ]
        );

    $response->assertStatus(302);

    //    dd(session('errors')->toArray());
    $response->assertSessionHasErrors([
        'media.0',
    ]);
    //    dd($response->getContent());
    //    $response->assertSessionHas('laravel-medialibrary-extensions.status.message', function ($message) {
    //        return str_contains($message, 'must not be greater than 100 kilobytes');
    //    });
});
