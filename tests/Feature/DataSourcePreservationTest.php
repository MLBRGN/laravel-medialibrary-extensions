<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerPermanentHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreUpdatedMediaAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreUpdatedMediaRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

beforeEach(function () {
    Storage::fake(config('medialibrary-extensions.media_disks.temporary'));
    Storage::fake('public');

    $this->mediaService = app(MediaService::class);
    $this->storeUpdatedAction = new StoreUpdatedMediaAction($this->mediaService);
    $this->getPermanentPreviewAction = new GetMediaPreviewerPermanentHTMLAction($this->mediaService);

    $this->initiatorId = 'initiator-datasource';
    $this->mediaManagerId = 'media-manager-datasource';
    $this->dataSource = 'demo';
    $this->model = $this->getTestBlogModel();

    // Re-register the media_demo connection to ensure it's in config
    $demoDatabasePath = __DIR__.'/../Support/demo.sqlite';
    config()->set('database.connections.media_demo', [
        'driver' => 'sqlite',
        'database' => $demoDatabasePath,
        'prefix' => '',
    ]);

    config()->set('medialibrary-extensions.data_sources.demo.connection', 'media_demo');

    DB::purge('media_demo');

    // Ensure blogs table exists on demo connection
    if (! Schema::connection('media_demo')->hasTable('blogs')) {
        Schema::connection('media_demo')->create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
    }
});

it('preserves dataSource after updating media and refreshing previews', function () {
    $file = UploadedFile::fake()->image('original.jpg');

    $demoModel = $this->model->replicate();
    $demoModel->setConnection('media_demo');
    $demoModel->save();

    $existingMedium = $demoModel
        ->addMedia($file)
        ->toMediaCollection('images');

    // 2. Update the media
    $updateFile = UploadedFile::fake()->image('updated.jpg');
    $updateRequest = StoreUpdatedMediaRequest::create('/update', 'POST', [
        'model_type' => get_class($demoModel),
        'model_id' => $demoModel->id,
        'medium_id' => $existingMedium->id,
        'collection' => 'images',
        'collections' => ['image' => 'images'],
        'temporary_upload_mode' => false,
        'initiator_id' => $this->initiatorId,
        'media_manager_id' => $this->mediaManagerId,
        'data_source' => $this->dataSource,
    ], [], [
        'file' => $updateFile,
    ]);
    $updateRequest->setLaravelSession(app('session.store'));
    $updateRequest->headers->set('Accept', 'application/json');

    $updateResponse = $this->storeUpdatedAction->execute($updateRequest);
    expect($updateResponse->status())->toBe(200);

    // 3. Request Preview refresh with dataSource
    $previewRequest = GetMediaManagerPreviewerHTMLRequest::create('/preview', 'GET', [
        'initiator_id' => $this->initiatorId,
        'model_type' => get_class($demoModel),
        'model_id' => $demoModel->id,
        'collections' => json_encode(['image' => 'images']),
        'options' => json_encode(['frontendTheme' => 'bootstrap-5']),
        'data_source' => $this->dataSource,
    ]);

    $previewResponse = $this->getPermanentPreviewAction->execute($previewRequest);
    $data = $previewResponse->getData(true);

    expect($data['success'])->toBeTrue();
    expect($data['html'])->toContain('updated.jpg');
});
