<?php

use Illuminate\Http\JsonResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\DestroyMediaAction;
use Mlbrgn\MediaLibraryExtensions\Actions\DestroyTemporaryUploadAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaManagerPreviewerHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetMediaAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetTemporaryUploadAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultipleMediaAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSingleMediaAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreUpdatedMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoAction;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\MediaManagerController;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyTemporaryUploadRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetMediumAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryUploadAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\UpdateMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('delegates store single', function () {
    $request = mock(StoreSingleRequest::class);
    $action = mock(StoreSingleMediaAction::class);
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
    $action = mock(DestroyMediaAction::class);
    $response = new JsonResponse(['deleted' => true]);

    $action->shouldReceive('execute')
        ->once()
        ->with($request)
        ->andReturn($response);

    $controller = new MediaManagerController;

    $result = $controller->destroy($request, $action);

    expect($result)->toBe($response);
});

it('delegates temporary upload destroy', function () {
    $request = mock(DestroyTemporaryUploadRequest::class);
    $action = mock(DestroyTemporaryUploadAction::class);
    $response = new JsonResponse(['deleted' => true]);

    $action->shouldReceive('execute')
        ->once()
        ->with($request)
        ->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->destroyTemporaryUpload($request, $action);

    expect($result)->toBe($response);
});

it('delegates set as first media', function () {
    $request = mock(SetMediumAsFirstRequest::class);
    $action = mock(SetMediaAsFirstAction::class);
    $response = new JsonResponse(['success' => true]);

    $action->shouldReceive('execute')
        ->once()
        ->with($request)
        ->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->setAsFirst($request, $action);

    expect($result)->toBe($response);
});

it('delegates set temporary upload as first', function () {
    $request = mock(SetTemporaryUploadAsFirstRequest::class);
    $action = mock(SetTemporaryUploadAsFirstAction::class);
    $response = new JsonResponse(['success' => true]);

    $action->shouldReceive('execute')
        ->once()
        ->with($request)
        ->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->setAsFirstTemporaryUpload($request, $action);

    expect($result)->toBe($response);
});

it('delegates save updated medium', function () {
    $request = mock(UpdateMediumRequest::class);
    $action = mock(StoreUpdatedMediumAction::class);
    $response = new JsonResponse(['saved' => true]);

    $action->shouldReceive('execute')
        ->once()
        ->with($request)
        ->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->saveUpdatedMedia($request, $action);

    expect($result)->toBe($response);
});

it('delegates save updated temporary upload', function () {
    $request = mock(UpdateMediumRequest::class);
    $action = mock(StoreUpdatedMediumAction::class);
    $response = new JsonResponse(['saved' => true]);

    $action->shouldReceive('execute')
        ->once()
        ->with($request)
        ->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->saveUpdatedTemporaryUpload($request, $action);

    expect($result)->toBe($response);
});

it('delegates get updated previewer html', function () {
    $request = mock(GetMediaManagerPreviewerHTMLRequest::class);
    $action = mock(GetMediaManagerPreviewerHTMLAction::class);
    $response = new JsonResponse(['html' => '<div>preview</div>']);

    $action->shouldReceive('execute')
        ->once()
        ->with($request)
        ->andReturn($response);

    $controller = new MediaManagerController;
    $result = $controller->getUpdatedMediaManagerPreviewerHTML($request, $action);

    expect($result)->toBe($response);
});
