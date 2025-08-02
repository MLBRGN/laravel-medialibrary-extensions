<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\DeleteMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\DeleteTemporaryUploadAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SaveUpdatedMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetMediumAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetTemporaryUploadAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultipleMediaAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSingleMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeMediaAction;
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

//use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerController extends Controller
{

    public function store(MediaManagerUploadSingleRequest $request, StoreSingleMediumAction $storeSingleMediumAction): RedirectResponse|JsonResponse
    {
        return $storeSingleMediumAction->execute($request);
    }

    public function storeMany(MediaManagerUploadMultipleRequest $request, StoreMultipleMediaAction $storeMultipleMediaAction): RedirectResponse|JsonResponse
    {
        return $storeMultipleMediaAction->execute($request);
    }

    public function storeYouTube(MediaManagerUploadYouTubeRequest $request, StoreYouTubeMediaAction $storeYouTubeMediaAction): RedirectResponse|JsonResponse
    {
        return $storeYouTubeMediaAction->execute($request);
    }

    public function destroy(MediaManagerDestroyRequest $request, Media $media, DeleteMediumAction $deleteMediumAction): RedirectResponse|JsonResponse
    {
        return $deleteMediumAction->execute($request, $media);
    }

    public function temporaryUploadDestroy(MediaManagerTemporaryUploadDestroyRequest $request, TemporaryUpload $temporaryUpload, DeleteTemporaryUploadAction $deleteTemporaryUploadAction): RedirectResponse|JsonResponse
    {
        return $deleteTemporaryUploadAction->execute($request, $temporaryUpload);
    }

    public function setAsFirst(SetAsFirstRequest $request, SetMediumAsFirstAction $setMediumAsFirstAction): RedirectResponse|JsonResponse
    {
        return $setMediumAsFirstAction->execute($request);
    }

    public function setTemporaryUploadAsFirst(SetTemporaryUploadAsFirstRequest $request, SetTemporaryUploadAsFirstAction $setTemporaryUploadAsFirstAction): RedirectResponse|JsonResponse
    {
        return $setTemporaryUploadAsFirstAction->execute($request);
    }

    public function saveUpdatedMedium(SaveUpdatedMediumRequest $request, SaveUpdatedMediumAction $saveUpdatedMediumAction): RedirectResponse|JsonResponse
    {
        return $saveUpdatedMediumAction->execute($request);
    }

    public function getUpdatedPreviewerHTML(GetMediaPreviewerHTMLRequest $request, GetMediaPreviewerHTMLAction $getMediaPreviewerHTMLAction): JsonResponse
    {
        return $getMediaPreviewerHTMLAction->execute($request);
    }

}
