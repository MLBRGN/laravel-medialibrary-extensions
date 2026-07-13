<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\View\Components\Preview\MediaPreviews;
use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\Debug;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
        $dataSource = $request->input('data_source', 'default');
        // Strict: only Base ID is accepted
        $baseId = (string) $request->input('base_id');
        // Derive instance ID server-side
        $instanceId = InstanceManager::getInstanceId($baseId);
        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        $singleMediaId = $request->input('single_media_id');
        $singleMediaId = ($singleMediaId !== 'null' && $singleMediaId !== null && $singleMediaId !== '') ? (int) $singleMediaId : null;
        $multiple = $request->boolean('multiple');
        $disabled = $request->boolean('disabled');
        $readonly = $request->boolean('readonly');
        $selectable = $request->boolean('selectable');
        $theme = $request->input('theme');
        // no clientToken

        $options = json_decode($request->input('options'), true) ?? [];

        if ($theme) {
            $options['theme'] = $theme;
        }

        $collections = json_decode($request->input('collections'), true) ?? [];
        $model = $this->mediaService->resolveModelById($modelType, $modelId, $dataSource);

        $collections = collect($collections)
            ->filter(fn ($collection) => ! empty($collection))
            ->values()
            ->all();

        $singleMedia = null;
        $totalMediaCount = 0;

        // counting media
        if ($singleMediaId !== null) {
            // have to query the model, don't use Media directly (this uses wrong db for demo pages)
            $singleMedia = $model->media()->findOrFail($singleMediaId);

            if ($singleMedia) {
                $totalMediaCount = 1;
            } else {
                throw new Exception(__('medialibrary-extensions::messages.medium_not_found'));
            }

        } else {
            $totalMediaCount = $this->mediaService->countModelMediaInCollections($model, $collections, $dataSource);
        }

        // Determine max and flags
        $maxMediaCount = $multiple
            ? (int) config('medialibrary-extensions.max_items_in_shared_media_collections', 10)
            : 1;
        $isAtMax = $totalMediaCount >= $maxMediaCount;

        $component = new MediaPreviews(
            id: $baseId,
            modelOrClassName: $model,
            collections: $collections,
            options: $options,
            singleMedia: $singleMedia,
            multiple: $multiple,
            disabled: $disabled,
            readonly: $readonly,
            selectable: $selectable,
            instanceId: $instanceId,
            dataSource: $dataSource,
        );

        $html = Blade::renderComponent($component);
        $debugHtml = null;

        if (config('medialibrary-extensions.debug') && $request->boolean('include_debug')) {
            $debugComponent = new Debug(
                modelOrClassName: $model,
                config: $component->getConfig(),
                options: $options,
            );

            $debugHtml = Blade::renderComponent($debugComponent);
        }

        return response()->json([
            'html' => $html,
            'debugHtml' => $debugHtml,
            'mediaCount' => $totalMediaCount,
            'maxMediaCount' => $maxMediaCount,
            'isAtMax' => $isAtMax,
            'success' => true,
            'instanceId' => $instanceId,
            'dataSource' => $dataSource,
            'target' => $baseId,
        ]);
    }
}
