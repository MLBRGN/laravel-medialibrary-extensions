<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeMediaAction;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\YouTubeService;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadYouTubeRequest;
use function Pest\Laravel\mock;

beforeEach(function () {
    Storage::fake('public');

    $this->mediaService = Mockery::mock(MediaService::class);
    $this->youTubeService = Mockery::mock(YouTubeService::class);

    $this->action = new StoreYouTubeMediaAction(
        mediaService: $this->mediaService,
        youTubeService: $this->youTubeService
    );

    Config::set('media-library-extensions.youtube_support_enabled', true);
    Config::set('media-library-extensions.upload_field_name_youtube', 'youtube_url');
});

it('aborts if youtube support is disabled', function () {
    Config::set('media-library-extensions.youtube_support_enabled', false);

    $request = MediaManagerUploadYouTubeRequest::create('/', 'POST');
    try {
        $this->action->execute($request);
        $this->fail('Expected HttpException was not thrown.');// fail the test if we get here
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        expect($e->getStatusCode())->toBe(403);
    }
});

it('stores temporary thumbnail successfully (JSON)', function () {
    $request = MediaManagerUploadYouTubeRequest::create('/', 'POST', [
        'temporary_upload' => true,
        'initiator_id' => 'abc',
        'collection_name' => 'test-collection',
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
            'initiatorId' => 'abc',
            'type' => 'success',
            'message' => __('media-library-extensions::messages.youtube_video_uploaded'),
        ]);
});

it('stores temporary thumbnail successfully (redirect)', function () {
    $request = MediaManagerUploadYouTubeRequest::create('/', 'POST', [
        'temporary_upload' => true,
        'initiator_id' => 'abc',
        'collection_name' => 'test-collection',
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
    expect($status['initiatorId'])->toBe('abc');
    expect($status['type'])->toBe('success');
    expect($status['message'])->toBe(__('media-library-extensions::messages.youtube_video_uploaded'));
});

it('returns error when temporary thumbnail fails to download (JSON)', function () {
    $request = MediaManagerUploadYouTubeRequest::create('/', 'POST', [
        'temporary_upload' => true,
        'initiator_id' => 'abc',
        'collection_name' => 'test-collection',
    ]);
    $request->headers->set('Accept', 'application/json');

    $this->youTubeService
        ->shouldReceive('storeTemporaryThumbnailFromRequest')
        ->once()
        ->andReturn(null);

    $response = $this->action->execute($request);
    expect($response->getData(true))
        ->toMatchArray([
            'initiatorId' => 'abc',
            'type' => 'error',
            'message' => __('media-library-extensions::messages.youtube_thumbnail_download_failed'),
        ]);
});

it('returns error when temporary thumbnail fails to download (redirect)', function () {
    $request = MediaManagerUploadYouTubeRequest::create('/', 'POST', [
        'temporary_upload' => true,
        'initiator_id' => 'abc',
        'collection_name' => 'test-collection',
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
    expect($status['initiatorId'])->toBe('abc');
    expect($status['type'])->toBe('error');
    expect($status['message'])->toBe(__('media-library-extensions::messages.youtube_thumbnail_download_failed'));
});

it('uploads youtube thumbnail to model successfully (JSON)', function () {
    $model = $this->getTestBlogModel();

    $request = MediaManagerUploadYouTubeRequest::create('/', 'POST', [
        'temporary_upload' => false,
        'initiator_id' => 'abc',
        'collection_name' => 'videos',
        'model_type' => get_class($model),
        'model_id' => $model->getKey(),
        'youtube_url' => 'https://youtu.be/test123',
    ]);
    $request->headers->set('Accept', 'application/json');

    $this->mediaService
        ->shouldReceive('resolveModel')
        ->once()
        ->with(get_class($model), $model->getKey())
        ->andReturn($model);

    $this->youTubeService
        ->shouldReceive('uploadThumbnailFromUrl')
        ->once()
        ->with($model, 'https://youtu.be/test123', 'videos');

    $response = $this->action->execute($request);
    expect($response->getData(true))
        ->toMatchArray([
            'initiatorId' => 'abc',
            'type' => 'success',
            'message' => __('media-library-extensions::messages.youtube_video_uploaded'),
        ]);
});

it('uploads youtube thumbnail to model successfully (redirect)', function () {
    $model = $this->getTestBlogModel();

    $request = MediaManagerUploadYouTubeRequest::create('/', 'POST', [
        'temporary_upload' => false,
        'initiator_id' => 'abc',
        'collection_name' => 'videos',
        'model_type' => get_class($model),
        'model_id' => $model->getKey(),
        'youtube_url' => 'https://youtu.be/test123',
    ]);
    $request->headers->remove('Accept');
    $request->setLaravelSession(app('session')->driver());

    $this->mediaService
        ->shouldReceive('resolveModel')
        ->once()
        ->with(get_class($model), $model->getKey())
        ->andReturn($model);

    $this->youTubeService
        ->shouldReceive('uploadThumbnailFromUrl')
        ->once()
        ->with($model, 'https://youtu.be/test123', 'videos');

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $session = $request->session();
    expect($session->has('laravel-medialibrary-extensions.status'))->toBeTrue();

    $status = $session->get('laravel-medialibrary-extensions.status');
    expect($status['initiatorId'])->toBe('abc');
    expect($status['type'])->toBe('success');
    expect($status['message'])->toBe(__('media-library-extensions::messages.youtube_video_uploaded'));
});

it('returns error when no youtube url provided for direct upload (JSON)', function () {
    $model = $this->getTestBlogModel();

    $request = MediaManagerUploadYouTubeRequest::create('/', 'POST', [
        'temporary_upload' => false,
        'initiator_id' => 'abc',
        'collection_name' => 'videos',
        'model_type' => get_class($model),
        'model_id' => $model->getKey(),
        // no youtube_url
    ]);
    $request->headers->set('Accept', 'application/json');

    $this->mediaService
        ->shouldReceive('resolveModel')
        ->once()
        ->andReturn($model);

    $response = $this->action->execute($request);
    expect($response->getData(true))
        ->toMatchArray([
            'initiatorId' => 'abc',
            'type' => 'error',
            'message' => __('media-library-extensions::messages.upload_no_youtube_url'),
        ]);
});

it('returns error when no youtube url provided for direct upload (redirect)', function () {
    $model = $this->getTestBlogModel();

    $request = MediaManagerUploadYouTubeRequest::create('/', 'POST', [
        'temporary_upload' => false,
        'initiator_id' => 'abc',
        'collection_name' => 'videos',
        'model_type' => get_class($model),
        'model_id' => $model->getKey(),
        // no youtube_url
    ]);
    $request->headers->remove('Accept');
    $request->setLaravelSession(app('session')->driver());

    $this->mediaService
        ->shouldReceive('resolveModel')
        ->once()
        ->andReturn($model);

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $session = $request->session();
    expect($session->has('laravel-medialibrary-extensions.status'))->toBeTrue();

    $status = $session->get('laravel-medialibrary-extensions.status');
    expect($status['initiatorId'])->toBe('abc');
    expect($status['type'])->toBe('error');
    expect($status['message'])->toBe(__('media-library-extensions::messages.upload_no_youtube_url'));
});

