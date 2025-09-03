<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Actions\DeleteMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\DeleteTemporaryUploadAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaManagerTinyMceAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreUpdatedMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetMediumAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetTemporaryUploadAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultipleMediaAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSingleMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerTinyMceRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerDestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerTemporaryUploadDestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreUpdatedMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryUploadAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerController extends Controller
{
    public function store(
        MediaManagerUploadSingleRequest $request,
        StoreSingleMediumAction $storeSingleMediumAction
    ): RedirectResponse|JsonResponse {
        return $storeSingleMediumAction->execute($request);
    }

    public function storeMany(
        MediaManagerUploadMultipleRequest $request,
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
        MediaManagerDestroyRequest $request,
        Media $media,
        DeleteMediumAction $deleteMediumAction
    ): RedirectResponse|JsonResponse {
        return $deleteMediumAction->execute($request, $media);
    }

    public function temporaryUploadDestroy(
        MediaManagerTemporaryUploadDestroyRequest $request,
        TemporaryUpload $temporaryUpload,
        DeleteTemporaryUploadAction $deleteTemporaryUploadAction
    ): RedirectResponse|JsonResponse {
        return $deleteTemporaryUploadAction->execute($request, $temporaryUpload);
    }

    public function setAsFirst(
        SetAsFirstRequest $request,
        SetMediumAsFirstAction $setMediumAsFirstAction
    ): RedirectResponse|JsonResponse {
        return $setMediumAsFirstAction->execute($request);
    }

    public function setTemporaryUploadAsFirst(
        SetTemporaryUploadAsFirstRequest $request,
        SetTemporaryUploadAsFirstAction $setTemporaryUploadAsFirstAction
    ): RedirectResponse|JsonResponse {
        return $setTemporaryUploadAsFirstAction->execute($request);
    }

    public function saveUpdatedMedium(
        StoreUpdatedMediumRequest $request,
        StoreUpdatedMediumAction $saveUpdatedMediumAction
    ): RedirectResponse|JsonResponse {
        return $saveUpdatedMediumAction->execute($request);
    }

    public function saveUpdatedTemporaryUpload(
        StoreUpdatedMediumRequest $request,
        StoreUpdatedMediumAction $saveUpdatedMediumAction
    ): RedirectResponse|JsonResponse {
        return $saveUpdatedMediumAction->execute($request);
    }

    public function getUpdatedPreviewerHTML(
        GetMediaPreviewerHTMLRequest $request,
        GetMediaPreviewerHTMLAction $getMediaPreviewerHTMLAction
    ): RedirectResponse|JsonResponse {
        return $getMediaPreviewerHTMLAction->execute($request);
    }

//    public function tinyMce(GetMediaManagerTinyMceRequest $request, GetMediaManagerTinyMceAction $getMediaManagerTinyMceAction): View
//    {
//        return $getMediaManagerTinyMceAction->execute($request);
//    }

    public function tinyMce(GetMediaManagerTinyMceRequest $request): View
    {
//        dd($request);
//        $frontendTheme = $request->input('frontend_theme') ? $request->input('frontend_theme') : config(['media-library-extensions.frontend_theme' => 'bootstrap-5']);

        $frontendTheme = 'plain';
        // Get the first existing model or create it if none exists
        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        $id = 'something_for_now';// TODO
        $imageCollection = $request->input('image_collection') ?? '';
        $audioCollection = $request->input('audio_collection') ?? '';
        $videoCollection = $request->input('video_collection') ?? '';
        $youtubeCollection = '';// TODO tinymce does not support youtube this way
        $documentCollection = '';// TODO tinymce does not support documents this way

        $temporaryUpload = false;

        $model = null;
        if ($modelType && $modelId) {
            $model = $modelType::findOrFail($modelId);
        }
        $modelOrClassName = $model ?? $modelType;
        return view('media-library-extensions::media-manager-tinymce-wrapper', compact(
            'modelOrClassName', 'temporaryUpload',
            'frontendTheme', 'id', 'imageCollection', 'documentCollection',
            'audioCollection', 'videoCollection', 'youtubeCollection'));
    }

}
