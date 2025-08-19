<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\UploadedFile;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultiplePermanentAction;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxMediaCount;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\YouTubeService;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;
use Spatie\MediaLibrary\HasMedia;

beforeEach(function () {
    $this->mediaService = \Mockery::mock(MediaService::class);
    $this->youTubeService = \Mockery::mock(YouTubeService::class);
    $this->action = new StoreMultiplePermanentAction($this->mediaService, $this->youTubeService);
});

test('it stores multiple valid files and returns JSON success', function () {
    $file1 = UploadedFile::fake()->image('photo1.jpg');
    $file2 = UploadedFile::fake()->image('photo2.jpg');
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);
    $this->mediaService
        ->shouldReceive('determineCollection')->twice()->andReturn('images');

    $uploadFieldNameMultiple = config('media-library-extensions.upload_field_name_multiple');
    $request = MediaManagerUploadMultipleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => 'xyz',
    ], [], [
        $uploadFieldNameMultiple => [$file1, $file2]
    ]);
    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'application/json');

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\JsonResponse::class)
        ->and($response->getData(true))
        ->toMatchArray([
            'initiatorId' => 'xyz',
            'type' => 'success',
            'message' => __('media-library-extensions::messages.upload_success'),
        ]);
});

test('it stores multiple valid files and returns redirect success', function () {
    $file = UploadedFile::fake()->image('photo.jpg');
    $model = $this->getTestBlogModel();
    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);
    $this->mediaService
        ->shouldReceive('determineCollection')->once()->andReturn('images');

    $uploadFieldNameMultiple = config('media-library-extensions.upload_field_name_multiple');
    $request = MediaManagerUploadMultipleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => 'abc',
    ], [], [
        $uploadFieldNameMultiple => [$file]
    ]);
    $request->setLaravelSession(app('session.store'));

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\RedirectResponse::class);
    $session = $request->session();

    expect($session->has('laravel-medialibrary-extensions.status'))->toBeTrue();

    $sessionData = $session->get('laravel-medialibrary-extensions.status');

    expect($sessionData['type'])->toBe('success');
    expect($sessionData['initiator_id'])->toBe('abc');
    expect($sessionData['message'])->toBe(__('media-library-extensions::messages.upload_success'));
});

test('it returns error if no files are given (JSON)', function () {
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);

    $request = MediaManagerUploadMultipleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => 'abc',
    ]);
    $request->headers->set('Accept', 'application/json');

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\JsonResponse::class)
      ->and($response->getData(true))
              ->toMatchArray([
                  'initiatorId' => 'abc',
                  'type' => 'error',
                  'message' => __('media-library-extensions::messages.upload_no_files'),
              ]);
});

test('it returns error if no files are given (redirect)', function () {
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);

    $request = MediaManagerUploadMultipleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => 'abc',
    ]);

    $request->setLaravelSession(app('session.store'));

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\RedirectResponse::class);

    $session = $request->session();

    expect($session->has('laravel-medialibrary-extensions.status'))->toBeTrue();

    $sessionData = $session->get('laravel-medialibrary-extensions.status');

    expect($sessionData['type'])->toBe('error');
    expect($sessionData['initiator_id'])->toBe('abc');
    expect($sessionData['message'])->toBe(__('media-library-extensions::messages.upload_no_files'));
});

test('it returns error if file has invalid mimetype (JSON)', function () {
    $file = UploadedFile::fake()->create('file.exe', 100, 'application/octet-stream');
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);
    $this->mediaService
        ->shouldReceive('determineCollection')->once()->andReturn(null);

    $uploadFieldNameMultiple = config('media-library-extensions.upload_field_name_multiple');
    $request = MediaManagerUploadMultipleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => 'bad',
    ], [], [
        $uploadFieldNameMultiple => [$file]
    ]);
    $request->headers->set('Accept', 'application/json');

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\JsonResponse::class)
        ->and($response->getData(true)['type'])->toBe('error')
        ->and($response->getData(true)['message'])->toBe(
            __('media-library-extensions::messages.upload_failed')
        );
});

test('it returns error if file has invalid mimetype (redirect)', function () {
    $file = UploadedFile::fake()->create('file.exe', 100, 'application/octet-stream');
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);
    $this->mediaService
        ->shouldReceive('determineCollection')->once()->andReturn(null);

    $uploadFieldNameMultiple = config('media-library-extensions.upload_field_name_multiple');
    $request = MediaManagerUploadMultipleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => 'bad',
    ], [], [
        $uploadFieldNameMultiple => [$file]
    ]);
    $request->setLaravelSession(app('session.store'));

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\RedirectResponse::class);

    $session = $request->session();

    expect($session->has('laravel-medialibrary-extensions.status'))->toBeTrue();

    $sessionData = $session->get('laravel-medialibrary-extensions.status');

    expect($sessionData['type'])->toBe('error');
    expect($sessionData['initiator_id'])->toBe('bad');
    expect($sessionData['message'])->toBe(__('media-library-extensions::messages.upload_failed'));
});

test('it returns error if max media count is exceeded (JSON)', function () {

    $model = $this->getTestBlogModel();
    $model->save(); // must be persisted for media attachment

    // Attach 1 existing media item
    $model->addMedia(UploadedFile::fake()->image('existing.jpg'))
        ->toMediaCollection('images');

    // Max = 2, but existing = 1 and adding 4 new => total = 5 > 2
    Config::set('media-library-extensions.route_middleware',[]);
    Config::set('media-library-extensions.max_items_in_shared_media_collections', 2);

    $file1 = UploadedFile::fake()->image('photo1.jpg');
    $file2 = UploadedFile::fake()->image('photo2.jpg');
    $file3 = UploadedFile::fake()->image('photo3.jpg');
    $file4 = UploadedFile::fake()->image('photo4.jpg');

    $uploadFieldNameMultiple = config('media-library-extensions.upload_field_name_multiple');

    $response = $this->withoutMiddleware(Authenticate::class)->postJson(
        route(config('media-library-extensions.route_prefix').'-media-upload-multiple'),
        [
            'model_type'   => $model->getMorphClass(),
            'model_id'     => $model->getKey(),
            'initiator_id' => 'overlimit',
            'image_collection' => 'images',
            'temporary_upload' => 'false',
            $uploadFieldNameMultiple => [$file1, $file2, $file3, $file4],
        ]
    );

    $response->assertStatus(422); // Validation failed
    $response->assertJsonValidationErrors(['media']); // or your field name

    $responseData = $response->json();

    expect($responseData['message'])->toBe(__('media-library-extensions::messages.this_collection_can_contain_up_to_:items_items', ['items' => config('media-library-extensions.max_items_in_shared_media_collections')]));
    expect($responseData['errors']['media'][0])->toBe(__('media-library-extensions::messages.this_collection_can_contain_up_to_:items_items', ['items' => config('media-library-extensions.max_items_in_shared_media_collections')]));
});

test('it returns error if max media count is exceeded (redirect)', function () {
    $model = $this->getTestBlogModel();
    $model->save(); // must be persisted for media attachment

    // Attach 1 existing media item
    $model->addMedia(UploadedFile::fake()->image('existing.jpg'))
        ->toMediaCollection('images');

    // Max = 2, but existing = 1 and adding 4 new => total = 5 > 2
    Config::set('media-library-extensions.route_middleware',[]);
    Config::set('media-library-extensions.max_items_in_shared_media_collections', 2);

    $file1 = UploadedFile::fake()->image('photo1.jpg');
    $file2 = UploadedFile::fake()->image('photo2.jpg');
    $file3 = UploadedFile::fake()->image('photo3.jpg');
    $file4 = UploadedFile::fake()->image('photo4.jpg');

    $uploadFieldNameMultiple = config('media-library-extensions.upload_field_name_multiple');

    $response = $this->withoutMiddleware(Authenticate::class)->post(
        route(config('media-library-extensions.route_prefix').'-media-upload-multiple'),
        [
            'model_type'   => $model->getMorphClass(),
            'model_id'     => $model->getKey(),
            'initiator_id' => 'overlimit',
            'image_collection' => 'images',
            'temporary_upload' => 'false',
            $uploadFieldNameMultiple => [$file1, $file2, $file3, $file4],
        ]
    );

    $response->assertStatus(302);

// Assert validation error is flashed to session
    $response->assertSessionHasErrors([
        'media' => __('media-library-extensions::messages.this_collection_can_contain_up_to_:items_items', ['items' => config('media-library-extensions.max_items_in_shared_media_collections')]),
    ]);
});


