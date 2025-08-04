<?php

use Illuminate\Http\JsonResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\DeleteMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\DeleteTemporaryUploadAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SaveUpdatedMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetMediumAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetTemporaryUploadAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultipleMediaAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSingleMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeMediaAction;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\MediaManagerController;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerDestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerTemporaryUploadDestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadYouTubeRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SaveUpdatedMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryUploadAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('delegates store single', function () {
    $request = mock(MediaManagerUploadSingleRequest::class);
    $action = mock(StoreSingleMediumAction::class);
    $response = new JsonResponse(['success' => true]);

    $action->shouldReceive('execute')->once()->with($request)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->store($request, $action);

    expect($result)->toBe($response);
});

it('delegates store many', function () {
    $request = mock(MediaManagerUploadMultipleRequest::class);
    $action = mock(StoreMultipleMediaAction::class);
    $response = new JsonResponse(['success' => true]);

    $action->shouldReceive('execute')->once()->with($request)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->storeMany($request, $action);

    expect($result)->toBe($response);
});

it('delegates store YouTube', function () {
    $request = mock(MediaManagerUploadYouTubeRequest::class);
    $action = mock(StoreYouTubeMediaAction::class);
    $response = new JsonResponse(['success' => true]);

    $action->shouldReceive('execute')->once()->with($request)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->storeYouTube($request, $action);

    expect($result)->toBe($response);
});

it('delegates destroy media', function () {
    $request = mock(MediaManagerDestroyRequest::class);
    $media = mock(Media::class);
    $action = mock(DeleteMediumAction::class);
    $response = new JsonResponse(['deleted' => true]);

    $action->shouldReceive('execute')->once()->with($request, $media)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->destroy($request, $media, $action);

    expect($result)->toBe($response);
});

it('delegates temporary upload destroy', function () {
    $request = mock(MediaManagerTemporaryUploadDestroyRequest::class);
    $tempUpload = mock(TemporaryUpload::class);
    $action = mock(DeleteTemporaryUploadAction::class);
    $response = new JsonResponse(['deleted' => true]);

    $action->shouldReceive('execute')->once()->with($request, $tempUpload)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->temporaryUploadDestroy($request, $tempUpload, $action);

    expect($result)->toBe($response);
});

it('delegates set as first media', function () {
    $request = mock(SetAsFirstRequest::class);
    $action = mock(SetMediumAsFirstAction::class);
    $response = new JsonResponse(['success' => true]);

    $action->shouldReceive('execute')->once()->with($request)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->setAsFirst($request, $action);

    expect($result)->toBe($response);
});

it('delegates set temporary upload as first', function () {
    $request = mock(SetTemporaryUploadAsFirstRequest::class);
    $action = mock(SetTemporaryUploadAsFirstAction::class);
    $response = new JsonResponse(['success' => true]);

    $action->shouldReceive('execute')->once()->with($request)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->setTemporaryUploadAsFirst($request, $action);

    expect($result)->toBe($response);
});

it('delegates save updated medium', function () {
    $request = mock(SaveUpdatedMediumRequest::class);
    $action = mock(SaveUpdatedMediumAction::class);
    $response = new JsonResponse(['saved' => true]);

    $action->shouldReceive('execute')->once()->with($request)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->saveUpdatedMedium($request, $action);

    expect($result)->toBe($response);
});

it('delegates save updated temporary upload', function () {
    $request = mock(SaveUpdatedMediumRequest::class);
    $action = mock(SaveUpdatedMediumAction::class);
    $response = new JsonResponse(['saved' => true]);

    $action->shouldReceive('execute')->once()->with($request)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->saveUpdatedTemporaryUpload($request, $action);

    expect($result)->toBe($response);
});

it('delegates get updated previewer html', function () {
    $request = mock(GetMediaPreviewerHTMLRequest::class);
    $action = mock(GetMediaPreviewerHTMLAction::class);
    $response = new JsonResponse(['html' => '<div>preview</div>']);

    $action->shouldReceive('execute')->once()->with($request)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->getUpdatedPreviewerHTML($request, $action);

    expect($result)->toBe($response);
});
