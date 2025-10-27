<?php

use Illuminate\Http\JsonResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\DestroyMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\DestroyTemporaryUploadAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaManagerPreviewerHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetMediumAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetTemporaryUploadAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultipleMediaAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSingleMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreUpdatedMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoAction;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\MediaManagerController;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyTemporaryMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetMediumAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryMediumAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\UpdateMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('delegates store single', function () {
    $request = mock(StoreSingleRequest::class);
    $action = mock(StoreSingleMediumAction::class);
    $response = new JsonResponse(['success' => true]);

    $action->shouldReceive('execute')->once()->with($request)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->store($request, $action);

    expect($result)->toBe($response);
});

it('delegates store many', function () {
    $request = mock(StoreMultipleRequest::class);
    $action = mock(StoreMultipleMediaAction::class);
    $response = new JsonResponse(['success' => true]);

    $action->shouldReceive('execute')->once()->with($request)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->storeMany($request, $action);

    expect($result)->toBe($response);
});

it('delegates store YouTube', function () {
    $request = mock(StoreYouTubeVideoRequest::class);
    $action = mock(StoreYouTubeVideoAction::class);
    $response = new JsonResponse(['success' => true]);

    $action->shouldReceive('execute')->once()->with($request)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->storeYouTube($request, $action);

    expect($result)->toBe($response);
});

it('delegates destroy media', function () {
    $request = mock(DestroyRequest::class);
    $media = mock(Media::class);
    $action = mock(DestroyMediumAction::class);
    $response = new JsonResponse(['deleted' => true]);

    $action->shouldReceive('execute')->once()->with($request, $media)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->destroy($request, $media, $action);

    expect($result)->toBe($response);
});

it('delegates temporary upload destroy', function () {
    $request = mock(DestroyTemporaryMediumRequest::class);
    $tempUpload = mock(TemporaryUpload::class);
    $action = mock(DestroyTemporaryUploadAction::class);
    $response = new JsonResponse(['deleted' => true]);

    $action->shouldReceive('execute')->once()->with($request, $tempUpload)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->temporaryUploadDestroy($request, $tempUpload, $action);

    expect($result)->toBe($response);
});

it('delegates set as first media', function () {
    $request = mock(SetMediumAsFirstRequest::class);
    $action = mock(SetMediumAsFirstAction::class);
    $response = new JsonResponse(['success' => true]);

    $action->shouldReceive('execute')->once()->with($request)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->setAsFirst($request, $action);

    expect($result)->toBe($response);
});

it('delegates set temporary upload as first', function () {
    $request = mock(SetTemporaryMediumAsFirstRequest::class);
    $action = mock(SetTemporaryUploadAsFirstAction::class);
    $response = new JsonResponse(['success' => true]);

    $action->shouldReceive('execute')->once()->with($request)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->setTemporaryUploadAsFirst($request, $action);

    expect($result)->toBe($response);
});

it('delegates save updated medium', function () {
    $request = mock(UpdateMediumRequest::class);
    $action = mock(StoreUpdatedMediumAction::class);
    $response = new JsonResponse(['saved' => true]);

    $action->shouldReceive('execute')->once()->with($request)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->saveUpdatedMedium($request, $action);

    expect($result)->toBe($response);
});

it('delegates save updated temporary upload', function () {
    $request = mock(UpdateMediumRequest::class);
    $action = mock(StoreUpdatedMediumAction::class);
    $response = new JsonResponse(['saved' => true]);

    $action->shouldReceive('execute')->once()->with($request)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->saveUpdatedTemporaryUpload($request, $action);

    expect($result)->toBe($response);
});

it('delegates get updated previewer html', function () {
    $request = mock(GetMediaManagerPreviewerHTMLRequest::class);
    $action = mock(GetMediaManagerPreviewerHTMLAction::class);
    $response = new JsonResponse(['html' => '<div>preview</div>']);

    $action->shouldReceive('execute')->once()->with($request)->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->getUpdatedMediaManagerPreviewerHTML($request, $action);

    expect($result)->toBe($response);
});
