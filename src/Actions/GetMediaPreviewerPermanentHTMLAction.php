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
        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        $mediumId = $request->input('medium_id');

        // Decode JSON strings to arrays
        $options = json_decode($request->input('options'), true) ?? [];
        $collections = json_decode($request->input('collections'), true) ?? [];

        if (isset($mediumId)) {
            // dd($mediumId);
        } else {
            $model = $this->mediaService->resolveModel(
                $modelType,
                $modelId,
            );
        }

        $collections = collect($collections)
            ->filter(fn ($collection) => ! empty($collection))
            ->all();

        $totalMediaCount = 0;

        foreach ($collections as $collectionName) {
            $totalMediaCount += $model->getMedia($collectionName)->count();
        }

        $component = new MediaManagerPreview(
            id: $initiatorId,
            modelOrClassName: $model,
            collections: $collections,
            options: $options,
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
