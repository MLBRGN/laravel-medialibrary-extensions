<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\UploadedFile;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultipleTemporaryAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\YouTubeService;

beforeEach(function () {
    $this->mediaService = \Mockery::mock(MediaService::class);
    $this->youTubeService = \Mockery::mock(YouTubeService::class);
    $this->action = new StoreMultipleTemporaryAction($this->mediaService, $this->youTubeService);
});

it('stores multiple valid files and returns JSON success', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $file1 = UploadedFile::fake()->image('photo1.jpg');
    $file2 = UploadedFile::fake()->image('photo2.jpg');
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('determineCollectionType')->twice()->andReturn('image');

    $uploadFieldNameMultiple = config('media-library-extensions.upload_field_name_multiple');
    $request = StoreMultipleRequest::create('/upload', 'POST', [
        'temporary_upload_mode' => true,
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['image' => 'images'],
    ], [], [
        $uploadFieldNameMultiple => [$file1, $file2],
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

it('stores multiple valid files and returns redirect success', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $file = UploadedFile::fake()->image('photo.jpg');
    $model = $this->getTestBlogModel();
    $this->mediaService
        ->shouldReceive('determineCollectionType')->once()->andReturn('image');

    $uploadFieldNameMultiple = config('media-library-extensions.upload_field_name_multiple');
    $request = StoreMultipleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['image' => 'images'],
    ], [], [
        $uploadFieldNameMultiple => [$file],
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

it('returns error if no files are given (JSON)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $model = $this->getTestBlogModel();

    $request = StoreMultipleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['image' => 'images'],
    ]);
    $request->headers->set('Accept', 'application/json');
    $request->setLaravelSession(app('session.store'));

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\JsonResponse::class)
        ->and($response->getData(true))
        ->toMatchArray([
            'initiatorId' => $initiatorId,
            'type' => 'error',
            'message' => __('media-library-extensions::messages.upload_no_files'),
        ]);
});

it('returns error if no files are given (redirect)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $model = $this->getTestBlogModel();

    $request = StoreMultipleRequest::create('/upload', 'POST', [
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
        ->shouldReceive('determineCollectionType')->once()->andReturn(null);

    $uploadFieldNameMultiple = config('media-library-extensions.upload_field_name_multiple');
    $request = StoreMultipleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['image' => 'images'],
    ], [], [
        $uploadFieldNameMultiple => [$file],
    ]);
    $request->headers->set('Accept', 'application/json');
    $request->setLaravelSession(app('session.store'));

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\JsonResponse::class)
        ->and($response->getData(true)['type'])->toBe('error')
        ->and($response->getData(true)['message'])->toBe(
            __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype')
        );
})->todo();

it('returns error if file has invalid mimetype (redirect)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $file = UploadedFile::fake()->create('file.exe', 100, 'application/octet-stream');
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('determineCollectionType')->once()->andReturn(null);

    $uploadFieldNameMultiple = config('media-library-extensions.upload_field_name_multiple');
    $request = StoreMultipleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['image' => 'images'],
    ], [], [
        $uploadFieldNameMultiple => [$file],
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
})->todo();

it('returns error if max media count is exceeded (JSON)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $model = $this->getTestBlogModel();
    $model->save(); // must be persisted for media attachment

    // Attach 1 existing media item
    $model->addMedia(UploadedFile::fake()->image('existing.jpg'))
        ->toMediaCollection('images');

    // Max = 2, but existing = 1 and adding 4 new => total = 5 > 2
    Config::set('media-library-extensions.route_middleware', []);
    Config::set('media-library-extensions.max_items_in_shared_media_collections', 2);

    $file1 = UploadedFile::fake()->image('photo1.jpg');
    $file2 = UploadedFile::fake()->image('photo2.jpg');
    $file3 = UploadedFile::fake()->image('photo3.jpg');
    $file4 = UploadedFile::fake()->image('photo4.jpg');

    $uploadFieldNameMultiple = config('media-library-extensions.upload_field_name_multiple');

    $response = $this->withoutMiddleware(Authenticate::class)->postJson(
        route(config('media-library-extensions.route_prefix').'-media-upload-multiple'),
        [
            'model_type' => $model->getMorphClass(),
            'model_id' => null,
            'initiator_id' => $initiatorId,
            'media_manager_id' => $mediaManagerId,
            'collections' => ['image' => 'images'],
            'temporary_upload_mode' => 'true',
            $uploadFieldNameMultiple => [$file1, $file2, $file3, $file4],
        ]
    );

    $response->assertStatus(422); // Validation failed
    $response->assertJsonValidationErrors(['media']); // or your field name

    $responseData = $response->json();

    expect($responseData['message'])->toBe(__('media-library-extensions::messages.this_collection_can_contain_up_to_:items_items', ['items' => config('media-library-extensions.max_items_in_shared_media_collections')]));
    expect($responseData['errors']['media'][0])->toBe(__('media-library-extensions::messages.this_collection_can_contain_up_to_:items_items', ['items' => config('media-library-extensions.max_items_in_shared_media_collections')]));
});

it('returns error if max media count is exceeded (redirect)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $model = $this->getTestBlogModel();
    $model->save(); // must be persisted for media attachment

    // Attach 1 existing media item
    $model->addMedia(UploadedFile::fake()->image('existing.jpg'))
        ->toMediaCollection('images');

    // Max = 2, but existing = 1 and adding 4 new => total = 5 > 2
    Config::set('media-library-extensions.route_middleware', []);
    Config::set('media-library-extensions.max_items_in_shared_media_collections', 2);

    $file1 = UploadedFile::fake()->image('photo1.jpg');
    $file2 = UploadedFile::fake()->image('photo2.jpg');
    $file3 = UploadedFile::fake()->image('photo3.jpg');
    $file4 = UploadedFile::fake()->image('photo4.jpg');

    $uploadFieldNameMultiple = config('media-library-extensions.upload_field_name_multiple');

    $response = $this->withoutMiddleware(Authenticate::class)->post(
        route(config('media-library-extensions.route_prefix').'-media-upload-multiple'),
        [
            'model_type' => $model->getMorphClass(),
            'model_id' => null,
            'initiator_id' => $initiatorId,
            'media_manager_id' => $mediaManagerId,
            'collections' => ['image' => 'images'],
            'temporary_upload_mode' => 'true',
            $uploadFieldNameMultiple => [$file1, $file2, $file3, $file4],
        ]
    );

    $response->assertStatus(302);

    // Assert validation error is flashed to session
    $response->assertSessionHasErrors([
        'media' => __('media-library-extensions::messages.this_collection_can_contain_up_to_:items_items', ['items' => config('media-library-extensions.max_items_in_shared_media_collections')]),
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

    $uploadFieldNameMultiple = config('media-library-extensions.upload_field_name_multiple');

    $response = $this
        ->withoutMiddleware(Authenticate::class)
        ->postJson(
            route(config('media-library-extensions.route_prefix').'-media-upload-multiple'),
            [
                'model_type' => $model->getMorphClass(),
                'model_id' => null,
                'initiator_id' => $initiatorId,
                'media_manager_id' => $mediaManagerId,
                'collections' => ['image' => 'images'],
                'temporary_upload_mode' => 'true',
                $uploadFieldNameMultiple => [$tooLargeFile],
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

    $uploadFieldNameMultiple = config('media-library-extensions.upload_field_name_multiple');

    $response = $this
        ->withoutMiddleware(Authenticate::class)
        ->post(
            route(config('media-library-extensions.route_prefix').'-media-upload-multiple'),
            [
                'model_type' => $model->getMorphClass(),
                'model_id' => null,
                'initiator_id' => $initiatorId,
                'media_manager_id' => $mediaManagerId,
                'collections' => ['image' => 'images'],
                'temporary_upload_mode' => 'true',
                $uploadFieldNameMultiple => [$tooLargeFile],
            ]
        );

    $response->assertStatus(302);

    $response->assertSessionHas('laravel-medialibrary-extensions.status.message', function ($message) {
        return str_contains($message, 'exceeds the maximum allowed size');
    });

});
