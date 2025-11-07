<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\View\Components\Preview\MediaPreviews;

class GetMediaPreviewerPermanentHTMLAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    /**
     * @throws Exception
     */
    public function execute(GetMediaManagerPreviewerHTMLRequest $request): JsonResponse|Response
    {
        $initiatorId = $request->input('initiator_id');
        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        $singleMediumId = $request->input('single_medium_id');
        $singleMediumId = ($singleMediumId !== 'null' && $singleMediumId !== null && $singleMediumId !== '') ? (int) $singleMediumId : null;
        $multiple = $request->boolean('multiple');
        $disabled = $request->boolean('disabled');
        $readonly = $request->boolean('readonly');
        $selectable = $request->boolean('selectable');

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
            // have to query the model, don't use Media directly (this uses wrong db for demo pages)
            $singleMedium = $model->media()->findOrFail($singleMediumId);

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

        $component = new MediaPreviews(
            id: $initiatorId,
            modelOrClassName: $model,
            collections: $collections,
            options: $options,
            singleMedium: $singleMedium,
            multiple: $multiple,
            disabled: $disabled,
            readonly: $readonly,
            selectable: $selectable,
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
