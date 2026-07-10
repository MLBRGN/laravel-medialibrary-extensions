<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoTemporaryAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\YouTubeService;
use Symfony\Component\HttpKernel\Exception\HttpException;

beforeEach(function () {
    Storage::fake('public');
    Config::set('medialibrary-extensions.youtube_support_enabled', true);
});

it('aborts if youtube support is disabled', function () {
    Config::set('medialibrary-extensions.youtube_support_enabled', false);

    $request = StoreYouTubeVideoRequest::create('/', 'POST');
    $mediaService = app(MediaService::class);
    $youTubeService = app(YouTubeService::class);
    $action = new StoreYouTubeVideoTemporaryAction($mediaService, $youTubeService);

    try {
        $action->execute($request);
        $this->fail('Expected HttpException was not thrown.'); // fail the test if we get here
    } catch (HttpException $e) {
        expect($e->getStatusCode())->toBe(403);
    }
});

it('stores temporary thumbnail successfully (JSON)', function () {
    $baseId = 'initiator-456';
    $baseId = 'media-manager-123';

    $tempUpload = $this->getTemporaryUpload();

    $request = StoreYouTubeVideoRequest::create('/', 'POST', [
        'temporary_upload_mode' => true,
        'base_id' => $baseId,
        'youtube_url' => 'https://www.youtube.com/watch?v=abc',
        'collections' => ['image' => 'images'],
        'youtube_collection' => 'test-collection',
        'temporary_upload_id' => $tempUpload->id,
        'multiple' => 'false',
        'model_type' => 'App\Models\Post',
        'data_source' => 'default',
    ]);
    $request->setLaravelSession(app('session.store'));

    $request->headers->set('Accept', 'application/json');

    $mediaService = app(MediaService::class);
    $youTubeService = Mockery::mock(YouTubeService::class);
    $action = new StoreYouTubeVideoTemporaryAction($mediaService, $youTubeService);

    $youTubeService->shouldReceive('storeTemporaryThumbnailFromRequest')
        ->once()
        ->andReturn($tempUpload);

    $response = $action->execute($request);

    expect($response->getData(true))
        ->toMatchArray([
            'baseId' => $baseId,
            'type' => 'success',
            'message' => __('medialibrary-extensions::messages.youtube_video_uploaded'),
        ]);
});

it('stores temporary thumbnail successfully (redirect)', function () {
    $baseId = 'initiator-456';
    $baseId = 'media-manager-123';
    $request = StoreYouTubeVideoRequest::create('/', 'POST', [
        'temporary_upload_mode' => true,
        'youtube_url' => 'https://www.youtube.com/watch?v=abc',
        'base_id' => $baseId,
        'collections' => ['image' => 'images'],
        'youtube_collection' => 'test-collection',
        'data_source' => 'default',
    ]);

    // Remove JSON Accept header to simulate the redirect request
    $request->headers->remove('Accept');
    $request->setLaravelSession(app('session')->driver());

    $tempUpload = $this->getTemporaryUpload();

    $mediaService = app(MediaService::class);
    $youTubeService = Mockery::mock(YouTubeService::class);
    $action = new StoreYouTubeVideoTemporaryAction($mediaService, $youTubeService);

    $youTubeService->shouldReceive('storeTemporaryThumbnailFromRequest')
        ->once()
        ->andReturn($tempUpload);

    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $session = $request->session();
    expect($session->has(status_session_prefix()))->toBeTrue();

    $sessionData = $session->get(status_session_prefix());
    expect($sessionData['base_id'])->toBe($baseId);
    expect($sessionData['type'])->toBe('success');
    expect($sessionData['message'])->toBe(__('medialibrary-extensions::messages.youtube_video_uploaded'));
});

it('returns error when temporary thumbnail fails to download (JSON)', function () {
    $baseId = 'initiator-456';
    $baseId = 'media-manager-123';
    $request = StoreYouTubeVideoRequest::create('/', 'POST', [
        'temporary_upload_mode' => true,
        'youtube_url' => 'https://www.youtube.com/watch?v=abc',
        'base_id' => $baseId,
        'collections' => ['image' => 'images'],
        'youtube_collection' => 'test-collection',
        'data_source' => 'default',
    ]);
    $request->headers->set('Accept', 'application/json');

    $this->youTubeService = Mockery::mock(YouTubeService::class);
    $mediaService = app(MediaService::class);
    $this->action = new StoreYouTubeVideoTemporaryAction($mediaService, $this->youTubeService);

    $this->youTubeService
        ->shouldReceive('storeTemporaryThumbnailFromRequest')
        ->once()
        ->andReturn(null);

    $response = $this->action->execute($request);
    expect($response->getData(true))
        ->toMatchArray([
            'baseId' => $baseId,
            'type' => 'error',
            'message' => __('medialibrary-extensions::messages.youtube_thumbnail_download_failed'),
        ]);
});

it('returns error when temporary thumbnail fails to download (redirect)', function () {
    $baseId = 'initiator-456';
    $baseId = 'media-manager-123';
    $request = StoreYouTubeVideoRequest::create('/', 'POST', [
        'temporary_upload_mode' => true,
        'youtube_url' => 'https://www.youtube.com/watch?v=abc',
        'base_id' => $baseId,
        'collections' => ['image' => 'images'],
        'youtube_collection' => 'test-collection',
        'data_source' => 'default',
    ]);
    $request->headers->remove('Accept');
    $request->setLaravelSession(app('session')->driver());

    $this->youTubeService = Mockery::mock(YouTubeService::class);
    $mediaService = app(MediaService::class);
    $this->action = new StoreYouTubeVideoTemporaryAction($mediaService, $this->youTubeService);

    $this->youTubeService
        ->shouldReceive('storeTemporaryThumbnailFromRequest')
        ->once()
        ->andReturn(null);

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $session = $request->session();
    expect($session->has(status_session_prefix()))->toBeTrue();

    $sessionData = $session->get(status_session_prefix());
    expect($sessionData['base_id'])->toBe($baseId);
    expect($sessionData['type'])->toBe('error');
    expect($sessionData['message'])->toBe(__('medialibrary-extensions::messages.youtube_thumbnail_download_failed'));
});

it('returns error when no youtube url provided for direct upload (JSON)', function () {
    $baseId = 'initiator-456';
    $baseId = 'media-manager-123';
    $model = $this->getTestBlogModel();

    $request = StoreYouTubeVideoRequest::create('/', 'POST', [
        'temporary_upload_mode' => false,
        'base_id' => $baseId,
        'collections' => ['image' => 'images'],
        'youtube_collection' => 'videos',
        'model_type' => get_class($model),
        'model_id' => $model->getKey(),
        // no youtube_url
        'data_source' => 'default',
    ]);
    $request->headers->set('Accept', 'application/json');

    $mediaService = app(MediaService::class);
    $youTubeService = app(YouTubeService::class);
    $action = new StoreYouTubeVideoTemporaryAction($mediaService, $youTubeService);

    $response = $action->execute($request);
    expect($response->getData(true))
        ->toMatchArray([
            'baseId' => $baseId,
            'type' => 'error',
            'message' => __('medialibrary-extensions::messages.upload_no_youtube_url'),
        ]);
});

it('returns error when no youtube url provided for direct upload (redirect)', function () {
    $baseId = 'initiator-456';
    $baseId = 'media-manager-123';
    $model = $this->getTestBlogModel();

    $request = StoreYouTubeVideoRequest::create('/', 'POST', [
        'temporary_upload_mode' => false,
        'base_id' => $baseId,
        'collections' => ['image' => 'images'],
        'youtube_collection' => 'videos',
        'model_type' => get_class($model),
        'model_id' => $model->getKey(),
        // no youtube_url
        'data_source' => 'default',
    ]);
    $request->headers->remove('Accept');
    $request->setLaravelSession(app('session')->driver());

    $mediaService = app(MediaService::class);
    $youTubeService = app(YouTubeService::class);
    $action = new StoreYouTubeVideoTemporaryAction($mediaService, $youTubeService);

    $response = $action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $session = $request->session();
    expect($session->has(status_session_prefix()))->toBeTrue();

    $sessionData = $session->get(status_session_prefix());
    expect($sessionData['base_id'])->toBe($baseId);
    expect($sessionData['type'])->toBe('error');
    expect($sessionData['message'])->toBe(__('medialibrary-extensions::messages.upload_no_youtube_url'));
});
