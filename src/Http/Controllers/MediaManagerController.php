<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Actions\DestroyMediaAction;
use Mlbrgn\MediaLibraryExtensions\Actions\DestroyTemporaryUploadAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaManagerLabPreviewerHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaManagerPreviewerHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaManagerTinyMceAction;
use Mlbrgn\MediaLibraryExtensions\Actions\RestoreOriginalMediaAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetMediaAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetTemporaryUploadAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultipleMediaAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSingleMediaAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreUpdatedMediaAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyTemporaryUploadRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerLabPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerTinyMceRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\RestoreOriginalMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetMediumAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryUploadAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreUpdatedMediaRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;

class MediaManagerController extends Controller
{
    public function store(
        StoreSingleRequest $request,
        StoreSingleMediaAction $storesingleMediaAction
    ): RedirectResponse|JsonResponse {
        return $storesingleMediaAction->execute($request);
    }

    public function storeMany(
        StoreMultipleRequest $request,
        StoreMultipleMediaAction $storeMultipleMediaAction
    ): RedirectResponse|JsonResponse {
        return $storeMultipleMediaAction->execute($request);
    }

    public function storeYouTube(
        StoreYouTubeVideoRequest $request,
        StoreYouTubeVideoAction $storeYouTubeMediaAction
    ): RedirectResponse|JsonResponse {
        return $storeYouTubeMediaAction->execute($request);
    }

    public function destroy(
        DestroyRequest $request,
        DestroyMediaAction $deleteMediumAction,
    ): RedirectResponse|JsonResponse {
        return $deleteMediumAction->execute($request);
    }

    public function destroyTemporaryUpload(
        DestroyTemporaryUploadRequest $request,
        DestroyTemporaryUploadAction $deleteTemporaryUploadAction,
    ): RedirectResponse|JsonResponse {
        return $deleteTemporaryUploadAction->execute($request);
    }

    public function setAsFirst(
        SetMediumAsFirstRequest $request,
        SetMediaAsFirstAction $setMediumAsFirstAction
    ): RedirectResponse|JsonResponse {
        return $setMediumAsFirstAction->execute($request);
    }

    public function setAsFirstTemporaryUpload(
        SetTemporaryUploadAsFirstRequest $request,
        SetTemporaryUploadAsFirstAction $setAsFirstTemporaryUploadAction
    ): RedirectResponse|JsonResponse {
        return $setAsFirstTemporaryUploadAction->execute($request);
    }

    public function storeUpdatedMedia(
        StoreUpdatedMediaRequest $request,
        StoreUpdatedMediaAction $storeUpdatedMediaAction,
    ): RedirectResponse|JsonResponse {
        return $storeUpdatedMediaAction->execute($request);
    }

    public function storeUpdatedTemporaryUpload(
        StoreUpdatedMediaRequest $request,
        StoreUpdatedMediaAction $storeUpdatedMediaAction,
    ): RedirectResponse|JsonResponse {
        return $storeUpdatedMediaAction->execute($request);
    }

    public function getUpdatedMediaManagerPreviewerHTML(
        GetMediaManagerPreviewerHTMLRequest $request,
        GetMediaManagerPreviewerHTMLAction $getMediaManagerPreviewerHTMLAction
    ): RedirectResponse|JsonResponse {
        Log::info('MediaManagerController - GetUpdatedMediaManagerPreviewerHTMLAction invoked');

        return $getMediaManagerPreviewerHTMLAction->execute($request);
    }

    public function getUpdatedMediaManagerLabPreviewerHTML(
        GetMediaManagerLabPreviewerHTMLRequest $request,
        GetMediaManagerLabPreviewerHTMLAction $getMediaManagerLabPreviewerHTMLAction
    ): RedirectResponse|JsonResponse {
        return $getMediaManagerLabPreviewerHTMLAction->execute($request);
    }

    public function restoreOriginalMedium(
        RestoreOriginalMediumRequest $request,
        string|int $mediaId,
        RestoreOriginalMediaAction $restoreMediumAction
    ): RedirectResponse|JsonResponse {
        return $restoreMediumAction->execute($request, $mediaId);
    }

    public function tinyMce(GetMediaManagerTinyMceRequest $request, GetMediaManagerTinyMceAction $getMediaManagerTinyMceAction): View
    {
        return $getMediaManagerTinyMceAction->execute($request);
    }
}
