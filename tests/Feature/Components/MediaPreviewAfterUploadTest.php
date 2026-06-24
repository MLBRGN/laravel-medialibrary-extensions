<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerPermanentHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerTemporaryHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultiplePermanentAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultipleTemporaryAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSinglePermanentAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSingleTemporaryAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoPermanentAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoTemporaryAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\UploadPreparerService;
use Mlbrgn\MediaLibraryExtensions\Services\YouTubeService;

beforeEach(function () {
    Storage::fake(config('medialibrary-extensions.media_disks.temporary'));
    Storage::fake('public');

    $this->mediaService = app(MediaService::class);
    $this->uploadPreparer = app(UploadPreparerService::class);

    $this->storePermanentAction = new StoreSinglePermanentAction($this->mediaService, $this->uploadPreparer);
    $this->storeTemporaryAction = new StoreSingleTemporaryAction($this->mediaService, $this->uploadPreparer);
    $this->getPermanentPreviewAction = new GetMediaPreviewerPermanentHTMLAction($this->mediaService);
    $this->getTemporaryPreviewAction = new GetMediaPreviewerTemporaryHTMLAction($this->mediaService);

    $this->initiatorId = 'initiator-123';
    $this->mediaManagerId = 'media-manager-123';
    $this->model = $this->getTestBlogModel();
});

it('loads previews successfully after a permanent single upload', function () {
    $file = UploadedFile::fake()->image('photo-perm.jpg');

    // 1. Upload
    $uploadRequest = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id,
        'initiator_id' => $this->initiatorId,
        'media_manager_id' => $this->mediaManagerId,
        'collections' => ['image' => 'images'],
    ], [], [
        'media' => $file,
    ]);
    $uploadRequest->setLaravelSession(app('session.store'));
    $uploadRequest->headers->set('Accept', 'application/json');

    $uploadResponse = $this->storePermanentAction->execute($uploadRequest);
    expect($uploadResponse->status())->toBe(200);

    // 2. Request Preview
    $previewRequest = GetMediaManagerPreviewerHTMLRequest::create('/preview', 'GET', [
        'initiator_id' => $this->initiatorId,
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id,
        'collections' => json_encode(['image' => 'images']),
        'options' => json_encode(['frontendTheme' => 'bootstrap-5']),
    ]);

    $previewResponse = $this->getPermanentPreviewAction->execute($previewRequest);

    $data = $previewResponse->getData(true);
    expect($data['success'])->toBeTrue();
    expect($data['mediaCount'])->toBe(1);
    expect($data['html'])->toContain('photo-perm.jpg');
    expect($data['html'])->not->toContain('mle-no-media');
});

it('loads previews successfully after a permanent multiple upload', function () {
    $files = [
        UploadedFile::fake()->image('photo1.jpg'),
        UploadedFile::fake()->image('photo2.jpg'),
    ];
    $initiatorId = 'multiple-perm';

    // 1. Upload
    $uploadRequest = StoreMultipleRequest::create('/upload', 'POST', [
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $this->mediaManagerId,
        'collections' => ['image' => 'images'],
    ], [], [
        'media' => $files,
    ]);
    $uploadRequest->setLaravelSession(app('session.store'));
    $uploadRequest->headers->set('Accept', 'application/json');

    $action = new StoreMultiplePermanentAction($this->mediaService);
    $uploadResponse = $action->execute($uploadRequest);
    expect($uploadResponse->status())->toBe(200);

    // 2. Request Preview
    $previewRequest = GetMediaManagerPreviewerHTMLRequest::create('/preview', 'GET', [
        'initiator_id' => $initiatorId,
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id,
        'collections' => json_encode(['image' => 'images']),
        'options' => json_encode(['frontendTheme' => 'bootstrap-5']),
    ]);

    $previewResponse = $this->getPermanentPreviewAction->execute($previewRequest);

    $data = $previewResponse->getData(true);
    expect($data['success'])->toBeTrue();
    expect($data['mediaCount'])->toBe(2);
    expect($data['html'])->toContain('photo1.jpg');
    expect($data['html'])->toContain('photo2.jpg');
});

it('loads previews successfully after a temporary single upload', function () {
    $file = UploadedFile::fake()->image('photo-temp-single.jpg');
    $instanceId = 'single-temp-instance';
    $initiatorId = 'single-temp';
    $clientToken = 'test-session-id-single';

    // 1. Upload
    $uploadRequest = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($this->model),
        'initiator_id' => $initiatorId,
        'media_manager_id' => $this->mediaManagerId,
        'collections' => ['image' => 'images'],
        'instance_id' => $instanceId,
        'client_token' => $clientToken,
        'temporary_upload_mode' => 'true',
        'data_source' => 'demo',
    ], [], [
        'media' => $file,
    ]);
    $uploadRequest->setLaravelSession(app('session.store'));
    $uploadRequest->headers->set('Accept', 'application/json');

    $uploadResponse = $this->storeTemporaryAction->execute($uploadRequest);

    expect($uploadResponse->status())->toBe(200);

    // 2. Request Preview
    $previewRequest = GetMediaManagerPreviewerHTMLRequest::create('/preview', 'GET', [
        'initiator_id' => $initiatorId,
        'model_type' => get_class($this->model),
        'collections' => json_encode(['image' => 'images']),
        'options' => json_encode(['frontendTheme' => 'bootstrap-5']),
        'temporary_upload_mode' => 'true',
        'instance_id' => $instanceId,
        'client_token' => $clientToken,
        'data_source' => 'demo',
    ]);
    $previewRequest->setLaravelSession($uploadRequest->session());

    $previewResponse = $this->getTemporaryPreviewAction->execute($previewRequest);

    $data = $previewResponse->getData(true);
    expect($data['success'])->toBeTrue();
    expect($data['mediaCount'])->toBe(1);
    expect($data['html'])->toContain('photo-temp-single.jpg');
});

it('loads previews successfully after a temporary multiple upload', function () {
    $files = [
        UploadedFile::fake()->image('photo-temp1.jpg'),
        UploadedFile::fake()->image('photo-temp2.jpg'),
    ];
    $instanceId = 'multiple-temp-instance';
    $initiatorId = 'multiple-temp';
    $clientToken = 'test-session-id';

    // 1. Upload
    $uploadRequest = StoreMultipleRequest::create('/upload', 'POST', [
        'model_type' => get_class($this->model),
        'initiator_id' => $initiatorId,
        'media_manager_id' => $this->mediaManagerId,
        'collections' => ['image' => 'images'],
        'instance_id' => $instanceId,
        'client_token' => $clientToken,
        'temporary_upload_mode' => 'true',
        'data_source' => 'demo',
    ], [], [
        'media' => $files,
    ]);
    $uploadRequest->setLaravelSession(app('session.store'));
    $uploadRequest->headers->set('Accept', 'application/json');

    $action = new StoreMultipleTemporaryAction($this->mediaService);
    $uploadResponse = $action->execute($uploadRequest);

    expect($uploadResponse->status())->toBe(200);

    // 2. Request Preview
    $previewRequest = GetMediaManagerPreviewerHTMLRequest::create('/preview', 'GET', [
        'initiator_id' => $initiatorId,
        'model_type' => get_class($this->model),
        'collections' => json_encode(['image' => 'images']),
        'options' => json_encode(['frontendTheme' => 'bootstrap-5']),
        'temporary_upload_mode' => 'true',
        'instance_id' => $instanceId,
        'client_token' => $clientToken,
        'data_source' => 'demo',
    ]);
    $previewRequest->setLaravelSession($uploadRequest->session());

    $previewResponse = $this->getTemporaryPreviewAction->execute($previewRequest);

    $data = $previewResponse->getData(true);
    expect($data['success'])->toBeTrue();
    expect($data['mediaCount'])->toBe(2);
    expect($data['html'])->toContain('photo-temp1.jpg');
    expect($data['html'])->toContain('photo-temp2.jpg');
});

it('loads previews successfully after a permanent YouTube upload', function () {
    $youtubeUrl = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
    $initiatorId = 'youtube-perm';

    // Mock YouTubeService
    $youtubeService = Mockery::mock(YouTubeService::class);
    $youtubeService->shouldReceive('getVideoId')->andReturn('dQw4w9WgXcQ');
    $mediaMock = $this->model->addMedia(UploadedFile::fake()->image('thumb.jpg'))
        ->toMediaCollection('youtube');
    $youtubeService->shouldReceive('uploadThumbnailFromUrl')->andReturn($mediaMock);
    app()->instance(YouTubeService::class, $youtubeService);

    // 1. Upload
    $uploadRequest = StoreYouTubeVideoRequest::create('/upload-youtube', 'POST', [
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $this->mediaManagerId,
        'youtube_url' => $youtubeUrl,
        'collections' => ['image' => 'images'],
        'youtube_collection' => 'youtube',
    ]);
    $uploadRequest->setLaravelSession(app('session.store'));
    $uploadRequest->headers->set('Accept', 'application/json');

    $action = new StoreYouTubeVideoPermanentAction($this->mediaService, $youtubeService);
    $uploadResponse = $action->execute($uploadRequest);
    expect($uploadResponse->status())->toBe(200);

    // 2. Request Preview
    $previewRequest = GetMediaManagerPreviewerHTMLRequest::create('/preview', 'GET', [
        'initiator_id' => $initiatorId,
        'model_type' => get_class($this->model),
        'model_id' => $this->model->id,
        'collections' => json_encode(['image' => 'images', 'youtube' => 'youtube']),
        'options' => json_encode(['frontendTheme' => 'bootstrap-5']),
    ]);

    $previewResponse = $this->getPermanentPreviewAction->execute($previewRequest);

    $data = $previewResponse->getData(true);
    // fwrite(STDERR, $data['html'] . PHP_EOL);
    expect($data['success'])->toBeTrue();
    expect($data['mediaCount'])->toBe(1);
    expect($data['html'])->toContain('thumb.jpg');
});

it('loads previews successfully after a temporary YouTube upload', function () {
    $youtubeUrl = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
    $instanceId = 'youtube-temp-instance';
    $initiatorId = 'youtube-temp';
    $clientToken = 'youtube-test-session';

    // Mock YouTubeService
    $youtubeService = Mockery::mock(YouTubeService::class);
    $youtubeService->shouldReceive('getVideoId')->andReturn('dQw4w9WgXcQ');
    $youtubeService->shouldReceive('storeTemporaryThumbnailFromRequest')->andReturn(new TemporaryUpload([
        'id' => 123,
        'disk' => 'local',
        'path' => 'temp/thumb.jpg',
        'name' => 'thumb.jpg',
        'file_name' => 'thumb.jpg',
        'collection_name' => 'youtube',
        'client_token' => $clientToken,
        'instance_id' => $instanceId,
        'size' => 1024,
        'mime_type' => 'image/jpeg',
    ]));
    app()->instance(YouTubeService::class, $youtubeService);

    // 1. Upload
    $uploadRequest = StoreYouTubeVideoRequest::create('/upload-youtube', 'POST', [
        'model_type' => get_class($this->model),
        'initiator_id' => $initiatorId,
        'media_manager_id' => $this->mediaManagerId,
        'youtube_url' => $youtubeUrl,
        'collections' => ['youtube' => 'youtube'],
        'youtube_collection' => 'youtube',
        'instance_id' => $instanceId,
        'client_token' => $clientToken,
        'temporary_upload_mode' => 'true',
        'data_source' => 'demo',
    ]);
    $uploadRequest->setLaravelSession(app('session.store'));
    $uploadRequest->headers->set('Accept', 'application/json');

    $action = new StoreYouTubeVideoTemporaryAction($this->mediaService, $youtubeService);
    $uploadResponse = $action->execute($uploadRequest);
    expect($uploadResponse->status())->toBe(200);

    // 2. Request Preview
    $previewRequest = GetMediaManagerPreviewerHTMLRequest::create('/preview', 'GET', [
        'initiator_id' => $initiatorId,
        'model_type' => get_class($this->model),
        'collections' => json_encode(['youtube' => 'youtube']),
        'options' => json_encode(['frontendTheme' => 'bootstrap-5']),
        'temporary_upload_mode' => 'true',
        'instance_id' => $instanceId,
        'client_token' => $clientToken,
        'data_source' => 'demo',
    ]);
    $previewRequest->setLaravelSession($uploadRequest->session());

    $previewResponse = $this->getTemporaryPreviewAction->execute($previewRequest);

    $data = $previewResponse->getData(true);
    expect($data['success'])->toBeTrue();
    expect($data['mediaCount'])->toBe(1);
    expect($data['html'])->toContain('thumb.jpg');
});
