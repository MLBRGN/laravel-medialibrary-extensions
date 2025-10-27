<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoTemporaryAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\YouTubeService;

beforeEach(function () {
    Storage::fake('public');

    $this->mediaService = Mockery::mock(MediaService::class);
    $this->youTubeService = Mockery::mock(YouTubeService::class);

    $this->action = new StoreYouTubeVideoTemporaryAction($this->mediaService, $this->youTubeService);

    Config::set('media-library-extensions.youtube_support_enabled', true);
    Config::set('media-library-extensions.upload_field_name_youtube', 'youtube_url');
});

it('aborts if youtube support is disabled', function () {
    Config::set('media-library-extensions.youtube_support_enabled', false);

    $request = StoreYouTubeVideoRequest::create('/', 'POST');
    try {
        $this->action->execute($request);
        $this->fail('Expected HttpException was not thrown.'); // fail the test if we get here
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        expect($e->getStatusCode())->toBe(403);
    }
});

it('stores temporary thumbnail successfully (JSON)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $request = StoreYouTubeVideoRequest::create('/', 'POST', [
        'temporary_upload_mode' => true,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'youtube_url' => 'https://www.youtube.com/watch?v=abc',
        'collections' => ['image' => 'images'],
        'youtube_collection' => 'test-collection',
    ]);
    $request->headers->set('Accept', 'application/json');

    $tempUpload = $this->getTemporaryUpload();
    $this->youTubeService
        ->shouldReceive('storeTemporaryThumbnailFromRequest')
        ->once()
        ->with($request)
        ->andReturn($tempUpload);

    $response = $this->action->execute($request);
    expect($response->getData(true))
        ->toMatchArray([
            'initiatorId' => $initiatorId,
            'type' => 'success',
            'message' => __('media-library-extensions::messages.youtube_video_uploaded'),
        ]);
});

it('stores temporary thumbnail successfully (redirect)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $request = StoreYouTubeVideoRequest::create('/', 'POST', [
        'temporary_upload_mode' => true,
        'youtube_url' => 'https://www.youtube.com/watch?v=abc',
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['image' => 'images'],
        'youtube_collection' => 'test-collection',
    ]);

    // Remove json Accept header to simulate redirect request
    $request->headers->remove('Accept');
    $request->setLaravelSession(app('session')->driver());

    $tempUpload = $this->getTemporaryUpload();
    $this->youTubeService
        ->shouldReceive('storeTemporaryThumbnailFromRequest')
        ->once()
        ->with($request)
        ->andReturn($tempUpload);

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $session = $request->session();
    expect($session->has('laravel-medialibrary-extensions.status'))->toBeTrue();

    $status = $session->get('laravel-medialibrary-extensions.status');
    expect($status['initiator_id'])->toBe($initiatorId);
    expect($status['type'])->toBe('success');
    expect($status['message'])->toBe(__('media-library-extensions::messages.youtube_video_uploaded'));
});

it('returns error when temporary thumbnail fails to download (JSON)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $request = StoreYouTubeVideoRequest::create('/', 'POST', [
        'temporary_upload_mode' => true,
        'youtube_url' => 'https://www.youtube.com/watch?v=abc',
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['image' => 'images'],
        'youtube_collection' => 'test-collection',
    ]);
    $request->headers->set('Accept', 'application/json');

    $this->youTubeService
        ->shouldReceive('storeTemporaryThumbnailFromRequest')
        ->once()
        ->andReturn(null);

    $response = $this->action->execute($request);
    expect($response->getData(true))
        ->toMatchArray([
            'initiatorId' => $initiatorId,
            'type' => 'error',
            'message' => __('media-library-extensions::messages.youtube_thumbnail_download_failed'),
        ]);
});

it('returns error when temporary thumbnail fails to download (redirect)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $request = StoreYouTubeVideoRequest::create('/', 'POST', [
        'temporary_upload_mode' => true,
        'youtube_url' => 'https://www.youtube.com/watch?v=abc',
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['image' => 'images'],
        'youtube_collection' => 'test-collection',
    ]);
    $request->headers->remove('Accept');
    $request->setLaravelSession(app('session')->driver());

    $this->youTubeService
        ->shouldReceive('storeTemporaryThumbnailFromRequest')
        ->once()
        ->andReturn(null);

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $session = $request->session();
    expect($session->has('laravel-medialibrary-extensions.status'))->toBeTrue();

    $status = $session->get('laravel-medialibrary-extensions.status');
    expect($status['initiator_id'])->toBe($initiatorId);
    expect($status['type'])->toBe('error');
    expect($status['message'])->toBe(__('media-library-extensions::messages.youtube_thumbnail_download_failed'));
});

it('returns error when no youtube url provided for direct upload (JSON)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $model = $this->getTestBlogModel();

    $request = StoreYouTubeVideoRequest::create('/', 'POST', [
        'temporary_upload_mode' => false,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['image' => 'images'],
        'youtube_collection' => 'videos',
        'model_type' => get_class($model),
        'model_id' => $model->getKey(),
        // no youtube_url
    ]);
    $request->headers->set('Accept', 'application/json');

    $response = $this->action->execute($request);
    expect($response->getData(true))
        ->toMatchArray([
            'initiatorId' => $initiatorId,
            'type' => 'error',
            'message' => __('media-library-extensions::messages.upload_no_youtube_url'),
        ]);
});

it('returns error when no youtube url provided for direct upload (redirect)', function () {
    $initiatorId = 'initiator-456';
    $mediaManagerId = 'media-manager-123';
    $model = $this->getTestBlogModel();

    $request = StoreYouTubeVideoRequest::create('/', 'POST', [
        'temporary_upload_mode' => false,
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'collections' => ['image' => 'images'],
        'youtube_collection' => 'videos',
        'model_type' => get_class($model),
        'model_id' => $model->getKey(),
        // no youtube_url
    ]);
    $request->headers->remove('Accept');
    $request->setLaravelSession(app('session')->driver());

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $session = $request->session();
    expect($session->has('laravel-medialibrary-extensions.status'))->toBeTrue();

    $status = $session->get('laravel-medialibrary-extensions.status');
    expect($status['initiator_id'])->toBe($initiatorId);
    expect($status['type'])->toBe('error');
    expect($status['message'])->toBe(__('media-library-extensions::messages.upload_no_youtube_url'));
});
