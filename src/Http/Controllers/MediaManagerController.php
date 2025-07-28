<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\Actions\DeleteMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SaveUpdatedMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetMediumAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultipleMediaAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSingleMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeMediaAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerDestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadYouTubeRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SaveUpdatedMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerPreview;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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

    public function setAsFirst(SetAsFirstRequest $request, SetMediumAsFirstAction $setMediumAsFirstAction): RedirectResponse|JsonResponse
    {
        return $setMediumAsFirstAction->execute($request);
    }

    public function saveUpdatedMedium(SaveUpdatedMediumRequest $request, SaveUpdatedMediumAction $saveUpdatedMediumAction): RedirectResponse|JsonResponse
    {
        return $saveUpdatedMediumAction->execute($request);
    }

    // TODO move to dedicated file or class
    // used by ajax to refresh previews of images after upload / delete / new order
    public function getMediaPreviewerHTML(GetMediaPreviewerHTMLRequest $request): Response|JsonResponse
    {
        $mediaService = app(MediaService::class);
        $initiatorId = $request->input('initiator_id');
        $id = $initiatorId;

        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        $model = $mediaService->resolveModel($modelType, $modelId);

        $imageCollection = $request->input('image_collection');
        $documentCollection = $request->input('document_collection');
        $youtubeCollection = $request->input('youtube_collection');

        $frontendTheme = $request->input('frontend_theme');

        $destroyEnabled = $request->input('destroy_enabled');
        $setAsFirstEnabled = $request->input('set_as_first_enabled');
        $showMediaUrl = $request->input('show_media_url');
        $showOrder = $request->input('show_order');

        $component = new MediaManagerPreview(
            id: $id,

            model: $model,

            imageCollection: $imageCollection,
            documentCollection: $documentCollection,
            youtubeCollection: $youtubeCollection,

            frontendTheme: $frontendTheme,

            destroyEnabled: $destroyEnabled,
            setAsFirstEnabled: $setAsFirstEnabled,
            showMediaUrl: $showMediaUrl,
            showOrder: $showOrder,
        );

        $html = Blade::renderComponent($component);

        return response()->json([
            'html' => $html,
            'success' => true,
            'target' => $initiatorId,
        ]);
    }

}
