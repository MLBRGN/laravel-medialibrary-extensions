<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ViewErrorBag;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreUpdatedMediumAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\UpdateMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

beforeEach(function () {
    Storage::fake('tmp');
});

it('replaces a permanent medium (JSON)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $file = UploadedFile::fake()->image('new.jpg');

    $testImage = $this->getFixtureUploadedFile('test.jpg');
    $existingMedium = $this->getTestBlogModel()
        ->addMedia($testImage)
        ->toMediaCollection('blog-images');

    $request = UpdateMediumRequest::create('/', 'POST', [
        'model_type' => get_class($this->getTestBlogModel()),
        'model_id' => $this->getTestBlogModel()->id,
        'medium_id' => $existingMedium->id,
        'collection' => 'images',
        'collections' => ['image' => 'images'],
        'temporary_upload_mode' => false,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
    ], [], ['file' => $file]);
    $request->headers->set('Accept', 'application/json');

    $mediaService = Mockery::mock(MediaService::class);
    $mediaService
        ->shouldReceive('resolveModel')
        ->once()
        ->andReturn($this->getTestBlogModel());

    $action = new StoreUpdatedMediumAction($mediaService);

    $response = $action->execute($request);

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getData(true))
        ->toMatchArray([
            'initiatorId' => $initiatorId,
            'type' => 'success',
            'message' => __('media-library-extensions::messages.medium_replaced'),
        ]);
    $this->assertDatabaseMissing('media', ['id' => $existingMedium->id]);
});

it('replaces a permanent medium (redirect)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $file = UploadedFile::fake()->image('new.jpg');

    $testImage = $this->getFixtureUploadedFile('test.jpg');
    $existingMedium = $this->getTestBlogModel()
        ->addMedia($testImage)
        ->toMediaCollection('blog-images');

    $request = UpdateMediumRequest::create('/', 'POST', [
        'model_type' => get_class($this->getTestBlogModel()),
        'model_id' => $this->getTestBlogModel()->id,
        'medium_id' => $existingMedium->id,
        'collection' => 'images',
        'collections' => ['image' => 'images'],
        'temporary_upload_mode' => false,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
    ], [], ['file' => $file]);
    $request->setLaravelSession(app('session')->driver());

    $mediaService = Mockery::mock(MediaService::class);
    $mediaService
        ->shouldReceive('resolveModel')
        ->once()
        ->andReturn($this->getTestBlogModel());

    $action = new StoreUpdatedMediumAction($mediaService);

    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $session = $request->session();
    expect($session->has('laravel-medialibrary-extensions.status'))->toBeTrue();

    $status = $session->get('laravel-medialibrary-extensions.status');
    expect($status['initiator_id'])->toBe($initiatorId);
    expect($status['type'])->toBe('success');
    expect($status['message'])->toBe(__('media-library-extensions::messages.medium_replaced'));
});

it('replaces a temporary upload (JSON)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $existingUpload = $this->getTemporaryUpload('old_temp_file.jpg');
    $file = UploadedFile::fake()->image('new_temp_file.jpg');

    $request = UpdateMediumRequest::create('/', 'POST', [
        'medium_id' => $existingUpload->id,
        'collection' => 'temp-images',
        'collections' => ['image' => 'images'],
        'temporary_upload_mode' => true,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
    ], [], ['file' => $file]);
    $request->headers->set('Accept', 'application/json');
    $request->setLaravelSession(app('session')->driver());

    config()->set('media-library-extensions.temporary_upload_disk', 'tmp');
    config()->set('media-library-extensions.temporary_upload_path', 'temp');

    $mediaService = Mockery::mock(MediaService::class);

    $action = new StoreUpdatedMediumAction($mediaService);

    $response = $action->execute($request);

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getData(true))->toMatchArray([
            'initiatorId' => $initiatorId,
            'type' => 'success',
            'message' => __('media-library-extensions::messages.medium_replaced'),
        ]);

    // TODO
    //    $temporaryUploadOld = TemporaryUpload::find($existingUpload->id);
    //    expect($temporaryUploadOld)->toBeNull();
    //    Storage::disk('tmp')->assertExists('temp/new_temp_file.jpg');
    //    $temporaryUploadNew = TemporaryUpload::where('name', 'new_temp_file.jpg')->first();
    //    expect($temporaryUploadNew)->not()->toBeNull();

});

use Illuminate\Support\Facades\Validator;

it('stores validation errors in initiator-specific error bag when not using XHR', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $file = UploadedFile::fake()->image('new.jpg');

    $model = $this->getTestBlogModel();

    $request = UpdateMediumRequest::create('/', 'POST', [
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->getKey(),
        'medium_id' => '123',
        'collections' => ['image' => 'images'],
        'temporary_upload_mode' => 'false',
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
    ], [], ['file' => $file]);

    $request->setLaravelSession(app('session')->driver());

    // Manually build and run the validator using rules from the FormRequest
    $validator = Validator::make($request->all(), (new UpdateMediumRequest)->rules());

    $this->assertTrue($validator->fails());

    // Flash errors into initiator-specific bag (like failedValidation does)
    $bagName = 'initiator_'.$request->input('initiator_id');
    $request->session()->flash(
        'errors',
        (new ViewErrorBag)->put($bagName, $validator->errors())
    );

    $errors = $request->session()->get('errors');
    $bag = $errors->getBag($bagName);

    expect($bag->any())->toBeTrue();
    expect($bag->first())->toBe('The collection field is required.');
});

it('preserves the priority custom property when replacing a permanent medium', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $file = UploadedFile::fake()->image('new.jpg');

    $model = $this->getTestBlogModel();

    // Create a medium with custom priority
    $testImage = $this->getFixtureUploadedFile('test.jpg');
    $existingMedium = $model->addMedia($testImage)
        ->withCustomProperties(['priority' => 42])
        ->toMediaCollection('blog-images');

    // Double-check initial priority
    expect($existingMedium->getCustomProperty('priority'))->toBe(42);

    // Prepare request
    $request = UpdateMediumRequest::create('/', 'POST', [
        'model_type' => get_class($model),
        'model_id' => $model->id,
        'medium_id' => $existingMedium->id,
        'collection' => 'blog-images',
        'collections' => ['image' => 'blog-images'],
        'temporary_upload_mode' => false,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
    ], [], ['file' => $file]);
    $request->headers->set('Accept', 'application/json');

    // Mock media service to resolve model correctly
    $mediaService = Mockery::mock(MediaService::class);
    $mediaService->shouldReceive('resolveModel')
        ->once()
        ->andReturn($model);

    // Run action
    $action = new StoreUpdatedMediumAction($mediaService);
    $response = $action->execute($request);

    // Response assertions
    expect($response->getStatusCode())->toBe(200);

    // Find the newly created medium
    $newMedium = $model->getMedia('blog-images')->last();

    // Assert that the old one was deleted
    $this->assertDatabaseMissing('media', ['id' => $existingMedium->id]);

    expect($newMedium->getCustomProperty('priority'))->toBe(42);
});

it('returns error response if collections array is missing', function () {
    $request = UpdateMediumRequest::create('/', 'POST', [
        'temporary_upload_mode' => false,
        'initiator_id' => 'x',
        'media_manager_id' => 'y',
    ], [], ['file' => UploadedFile::fake()->image('test.jpg')]);
    $request->headers->set('Accept', 'application/json');

    $mediaService = Mockery::mock(MediaService::class);
    $action = new StoreUpdatedMediumAction($mediaService);

    $response = $action->execute($request);

    expect($response->getStatusCode())->toBe(422)
        ->and($response->getData(true)['type'])->toBe('error');
});

it('logs a warning when existing medium is not found', function () {
    Log::spy();

    $model = $this->getTestBlogModel();
    $file = UploadedFile::fake()->image('new.jpg');

    $request = UpdateMediumRequest::create('/', 'POST', [
        'model_type' => get_class($model),
        'model_id' => $model->id,
        'medium_id' => 99999, // nonexistent
        'collection' => 'images',
        'collections' => ['image' => 'images'],
        'temporary_upload_mode' => false,
        'initiator_id' => 'test',
        'media_manager_id' => 'mgr',
    ], [], ['file' => $file]);
    $request->headers->set('Accept', 'application/json');

    $mediaService = Mockery::mock(MediaService::class);
    $mediaService->shouldReceive('resolveModel')->andReturn($model);

    $action = new StoreUpdatedMediumAction($mediaService);
    $action->execute($request);

    Log::shouldHaveReceived('warning')
        ->once()
        ->with(Mockery::on(fn($msg) => str_contains($msg, 'not found')));
})->skip();

