<?php

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Support\Facades\Blade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerPreview;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest;

class GetMediaPreviewerPermanentHTMLAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(GetMediaPreviewerHTMLRequest $request): JsonResponse|Response
    {
        $initiatorId = $request->input('initiator_id');
        $model = $this->mediaService->resolveModel(
            $request->input('model_type'),
            $request->input('model_id'),
        );

        $component = new MediaManagerPreview(
            id: $initiatorId,
            model: $model,
            imageCollection: $request->input('image_collection'),
            documentCollection: $request->input('document_collection'),
            youtubeCollection: $request->input('youtube_collection'),
            frontendTheme: $request->input('frontend_theme'),
            destroyEnabled: $request->input('destroy_enabled'),
            setAsFirstEnabled: $request->input('set_as_first_enabled'),
            showMediaUrl: $request->input('show_media_url'),
            showOrder: $request->input('show_order'),
        );

        $html = Blade::renderComponent($component);

        return response()->json([
            'html' => $html,
            'success' => true,
            'target' => $initiatorId,
        ]);
    }
}
