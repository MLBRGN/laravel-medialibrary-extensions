<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\View\Components\Preview\MediaGrid;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
        $singleMediumId = $request->input('single_medium_id');
        $singleMediumId = ($singleMediumId !== 'null' && $singleMediumId !== null && $singleMediumId !== '') ? (int) $singleMediumId : null;

        $options = json_decode($request->input('options'), true) ?? [];
        $collections = json_decode($request->input('collections'), true) ?? [];
        $model = $this->mediaService->resolveModel($modelType, $modelId);

        $collections = collect($collections)
            ->filter(fn ($collection) => ! empty($collection))
            ->values()
            ->all();

        $singleMedium = null;
        $totalMediaCount = 0;

        // counting media
        if ($singleMediumId !== null) {
            // count single medium
            $singleMedium = Media::query()->find($singleMediumId);

            if ($singleMedium) {
                $totalMediaCount = 1;
            } else {
                throw new Exception(__('media-library-extensions::messages.medium_not_found'));
            }

        } else {
            // Count all media in collections
            foreach ($collections as $collectionName) {
                $totalMediaCount += $model->getMedia($collectionName)->count();
            }
        }

        $component = new MediaGrid(
            id: $initiatorId,
            modelOrClassName: $model,
            collections: $collections,
            options: $options,
            singleMedium: $singleMedium,
            noWrapper: true
        );
//        $component = new MediaManagerPreview(
//            id: $initiatorId,
//            modelOrClassName: $model,
//            singleMedium: $singleMedium,
//            collections: $collections,
//            options: $options,
//        );

        $html = Blade::renderComponent($component);

        return response()->json([
            'html' => $html,
            'mediaCount' => $totalMediaCount,
            'success' => true,
            'target' => $initiatorId,
        ]);
    }
}
