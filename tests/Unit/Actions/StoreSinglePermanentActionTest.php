<?php

use Illuminate\Http\UploadedFile;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSinglePermanentAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

beforeEach(function () {
    $this->mediaService = \Mockery::mock(MediaService::class);
    $this->action = new StoreSinglePermanentAction($this->mediaService);
});

test('it stores file and returns JSON success', function () {
    $file1 = UploadedFile::fake()->image('photo1.jpg');
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);
    $this->mediaService
        ->shouldReceive('determineCollection')->once()->andReturn('images');

    $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');
    $request = MediaManagerUploadSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => 'xyz',
    ], [], [
        $uploadFieldNameSingle => $file1
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

test('it stores file and returns redirect success', function () {
    $file = UploadedFile::fake()->image('photo.jpg');
    $model = $this->getTestBlogModel();
    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);
    $this->mediaService
        ->shouldReceive('determineCollection')->once()->andReturn('images');

    $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');
    $request = MediaManagerUploadSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => 'abc',
    ], [], [
        $uploadFieldNameSingle => $file
    ]);
    $request->setLaravelSession(app('session.store'));

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\RedirectResponse::class);
    $session = $request->session();

    expect($session->has('laravel-medialibrary-extensions.status'))->toBeTrue();

    $sessionData = $session->get('laravel-medialibrary-extensions.status');

    expect($sessionData['type'])->toBe('success');
    expect($sessionData['initiatorId'])->toBe('abc');
    expect($sessionData['message'])->toBe(__('media-library-extensions::messages.upload_success'));
});

test('it returns error if no file is given (JSON)', function () {
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);

    $request = MediaManagerUploadSingleRequest::create('/upload', 'POST', [
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

test('it returns error if no file is given (redirect)', function () {
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);

    $request = MediaManagerUploadSingleRequest::create('/upload', 'POST', [
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
    expect($sessionData['initiatorId'])->toBe('abc');
    expect($sessionData['message'])->toBe(__('media-library-extensions::messages.upload_no_files'));
});

test('it returns error if file has invalid mimetype (JSON)', function () {
    $file = UploadedFile::fake()->create('file.exe', 100, 'application/octet-stream');
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);
    $this->mediaService
        ->shouldReceive('determineCollection')->once()->andReturn(null);

    $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');

    $request = MediaManagerUploadSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => 'bad',
    ], [], [
         $uploadFieldNameSingle => $file
    ]);
    $request->headers->set('Accept', 'application/json');

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\JsonResponse::class)
        ->and($response->getData(true)['type'])->toBe('error')
        ->and($response->getData(true)['message'])->toBe(
            __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype')
        );
});

test('it returns error if file has invalid mimetype (redirect)', function () {
    $file = UploadedFile::fake()->create('file.exe', 100, 'application/octet-stream');
    $model = $this->getTestBlogModel();

    $this->mediaService
        ->shouldReceive('resolveModel')->once()->andReturn($model);
    $this->mediaService
        ->shouldReceive('determineCollection')->once()->andReturn(null);

    $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');

    $request = MediaManagerUploadSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => 1,
        'initiator_id' => 'bad',
    ], [], [
        $uploadFieldNameSingle => $file
    ]);
    $request->setLaravelSession(app('session.store'));

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\RedirectResponse::class);

    $session = $request->session();

    expect($session->has('laravel-medialibrary-extensions.status'))->toBeTrue();

    $sessionData = $session->get('laravel-medialibrary-extensions.status');

    expect($sessionData['type'])->toBe('error');
    expect($sessionData['initiatorId'])->toBe('bad');
    expect($sessionData['message'])->toBe(__('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype'));
});
