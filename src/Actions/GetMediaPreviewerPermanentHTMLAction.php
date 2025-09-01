<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerPreview;

class GetMediaPreviewerPermanentHTMLAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    /**
     * @throws Exception
     */
    public function execute(GetMediaPreviewerHTMLRequest $request): JsonResponse|Response
    {
        $initiatorId = $request->input('initiator_id');
        $model = $this->mediaService->resolveModel(
            $request->input('model_type'),
            $request->input('model_id'),
        );

        $imageCollection = $request->input('image_collection', '');
        $documentCollection = $request->input('document_collection', '');
        $youtubeCollection = $request->input('youtube_collection', '');
        $videoCollection = $request->input('video_collection', '');
        $audioCollection = $request->input('audio_collection', '');

        $collections = collect([
            $imageCollection,
            $documentCollection,
            $youtubeCollection,
            $videoCollection,
            $audioCollection,
        ])
            ->filter(fn ($collection) => !empty($collection)) // removes null, '', false
            ->all();

        $totalMediaCount = 0;

        foreach ($collections as $collectionName) {
            $totalMediaCount += $model->getMedia($collectionName)->count();
        }

        $component = new MediaManagerPreview(
            modelOrClassName: $model,
            id: $initiatorId,
            imageCollection: $imageCollection,
            documentCollection: $documentCollection,
            youtubeCollection: $youtubeCollection,
            videoCollection: $videoCollection,
            audioCollection: $audioCollection,
            frontendTheme: $request->input('frontend_theme'),
            destroyEnabled: $request->input('destroy_enabled') === 'true',
            setAsFirstEnabled: $request->input('set_as_first_enabled') === 'true',
            showOrder: $request->input('show_order') === 'true',
            showMenu: $request->input('show_menu') === 'true',
            temporaryUploads: $request->input('temporary_uploads') === 'true',
            selectable: $request->input('selectable') === 'true',
        );

        $html = Blade::renderComponent($component);

        return response()->json([
            'html' => $html,
            'mediaCount' => $totalMediaCount,
            'success' => true,
            'target' => $initiatorId,
        ]);
    }
}
