<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Actions\DestroyMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\DestroyTemporaryUploadAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaManagerLabPreviewerHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaManagerTinyMceAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaManagerPreviewerHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Actions\RestoreOriginalMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetMediumAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetTemporaryUploadAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultipleMediaAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSingleMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreUpdatedMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyTemporaryMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerLabPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerTinyMceRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\RestoreOriginalMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetMediumAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryMediumAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\UpdateMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerController extends Controller
{
    public function store(
        StoreSingleRequest $request,
        StoreSingleMediumAction $storeSingleMediumAction
    ): RedirectResponse|JsonResponse {
        return $storeSingleMediumAction->execute($request);
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
        Media $media,
        DestroyMediumAction $deleteMediumAction
    ): RedirectResponse|JsonResponse {
        return $deleteMediumAction->execute($request, $media);
    }

    public function temporaryUploadDestroy(
        DestroyTemporaryMediumRequest $request,
        TemporaryUpload $temporaryUpload,
        DestroyTemporaryUploadAction $deleteTemporaryUploadAction
    ): RedirectResponse|JsonResponse {
        return $deleteTemporaryUploadAction->execute($request, $temporaryUpload);
    }

    public function setAsFirst(
        SetMediumAsFirstRequest $request,
        SetMediumAsFirstAction $setMediumAsFirstAction
    ): RedirectResponse|JsonResponse {
        return $setMediumAsFirstAction->execute($request);
    }

    public function setTemporaryUploadAsFirst(
        SetTemporaryMediumAsFirstRequest $request,
        SetTemporaryUploadAsFirstAction $setTemporaryUploadAsFirstAction
    ): RedirectResponse|JsonResponse {
        return $setTemporaryUploadAsFirstAction->execute($request);
    }

    public function saveUpdatedMedium(
        UpdateMediumRequest $request,
        StoreUpdatedMediumAction $saveUpdatedMediumAction
    ): RedirectResponse|JsonResponse {
        return $saveUpdatedMediumAction->execute($request);
    }

    public function saveUpdatedTemporaryUpload(
        UpdateMediumRequest $request,
        StoreUpdatedMediumAction $saveUpdatedMediumAction
    ): RedirectResponse|JsonResponse {
        return $saveUpdatedMediumAction->execute($request);
    }

    public function getUpdatedMediaManagerPreviewerHTML(
        GetMediaManagerPreviewerHTMLRequest $request,
        GetMediaManagerPreviewerHTMLAction $getMediaManagerPreviewerHTMLAction
    ): RedirectResponse|JsonResponse {
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
        Media $media,
        RestoreOriginalMediumAction $restoreMediumAction
    ): RedirectResponse|JsonResponse {
        return $restoreMediumAction->execute($request, $media);
    }

    public function tinyMce(GetMediaManagerTinyMceRequest $request, GetMediaManagerTinyMceAction $getMediaManagerTinyMceAction): View
    {
        return $getMediaManagerTinyMceAction->execute($request);
    }
}
