<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreUpdatedMediumAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreUpdatedMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Illuminate\Support\ViewErrorBag;

beforeEach(function () {
    Storage::fake('tmp');
});

it('replaces a permanent medium (JSON)', function () {
    $file = UploadedFile::fake()->image('new.jpg');

    $testImage = $this->getUploadedFile('test.jpg');
    $existingMedium = $this->getTestBlogModel()
        ->addMedia($testImage)
        ->toMediaCollection('blog-images');

    $request = StoreUpdatedMediumRequest::create('/', 'POST', [
        'model_type' => get_class($this->getTestBlogModel()),
        'model_id' => $this->getTestBlogModel()->id,
        'medium_id' => $existingMedium->id,
        'collection' => 'images',
        'temporary_upload' => false,
        'initiator_id' => 'eg'
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
    ->and($response->getData(true))  ->toMatchArray([
            'initiatorId' => 'eg',
            'type' => 'success',
            'message' => __('media-library-extensions::messages.medium_replaced'),
        ]);
    $this->assertDatabaseMissing('media', ['id' => $existingMedium->id]);
});


it('replaces a permanent medium (redirect)', function () {
    $file = UploadedFile::fake()->image('new.jpg');

    $testImage = $this->getUploadedFile('test.jpg');
    $existingMedium = $this->getTestBlogModel()
        ->addMedia($testImage)
        ->toMediaCollection('blog-images');

    $request = StoreUpdatedMediumRequest::create('/', 'POST', [
        'model_type' => get_class($this->getTestBlogModel()),
        'model_id' => $this->getTestBlogModel()->id,
        'medium_id' => $existingMedium->id,
        'collection' => 'images',
        'temporary_upload' => false,
        'initiator_id' => 'eg'
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
    expect($status['initiator_id'])->toBe('eg');
    expect($status['type'])->toBe('success');
    expect($status['message'])->toBe(__('media-library-extensions::messages.medium_replaced'));
});

it('replaces a temporary upload (JSON)', function () {
    $existingUpload = $this->getTemporaryUpload('old_temp_file.jpg');
    $file = UploadedFile::fake()->image('new_temp_file.jpg');

    $request = StoreUpdatedMediumRequest::create('/', 'POST', [
        'medium_id' => $existingUpload->id,
        'collection' => 'temp-images',
        'temporary_upload' => true,
        'initiator_id' => 'temp_upload_test'
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
        'initiatorId' => 'temp_upload_test',
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
    $file = UploadedFile::fake()->image('new.jpg');

    $model = $this->getTestBlogModel();

    $request = StoreUpdatedMediumRequest::create('/', 'POST', [
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->getKey(),
        'medium_id' => '123',
        'temporary_upload' => 'false',
        'initiator_id' => 'eg',
    ], [], ['file' => $file]);

    $request->setLaravelSession(app('session')->driver());

    // Manually build and run the validator using rules from the FormRequest
    $validator = Validator::make($request->all(), (new StoreUpdatedMediumRequest)->rules());

    $this->assertTrue($validator->fails());

    // Flash errors into initiator-specific bag (like failedValidation does)
    $bagName = 'initiator_'.$request->input('initiator_id');
    $request->session()->flash(
        'errors',
        (new ViewErrorBag())->put($bagName, $validator->errors())
    );

    $errors = $request->session()->get('errors');
    $bag = $errors->getBag($bagName);

    expect($bag->any())->toBeTrue();
    expect($bag->first())->toBe('The collection field is required.');
});
