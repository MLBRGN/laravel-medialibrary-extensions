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
