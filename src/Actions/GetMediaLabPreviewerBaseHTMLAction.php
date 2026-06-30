<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaLabPreviewerBaseHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\View\Components\Lab\LabPreviewBase;
use Mlbrgn\MediaLibraryExtensions\View\Components\Lab\LabPreviewOriginal;
use Mlbrgn\MediaLibraryExtensions\View\Components\Lab\LabPreviews;
use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\Debug;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GetMediaLabPreviewerBaseHTMLAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    /**
     * @throws Exception
     */
    public function execute(GetMediaLabPreviewerBaseHTMLRequest $request): JsonResponse|Response
    {
        // Read the single authoritative Base ID
        $baseId = (string) $request->input('base_id');
        $mediumId = $request->input('medium_id');
        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        $theme = $request->input('theme');
        $options = json_decode($request->input('options'), true) ?? [];
        $dataSource = $request->input('data_source');

//        $part = $request->input('part', 'all');

        if ($theme) {
            $options['theme'] = $theme;
        }

        $model = $this->mediaService->resolveModelById($modelType, $modelId, $dataSource);

        // have to query the model, don't use Media directly (this uses wrong db for demo pages)
        $medium = $model->media()->find($mediumId);

        if (! $medium) {
            Log::error('GetMediaLabPreviewerHTMLAction - medium with mediumId: ' . $mediumId . ' not found');
            throw new Exception(__('medialibrary-extensions::messages.medium_not_found'));

//            return MediaResponse::error(
//                $request,
//                $baseId,
//                __('medialibrary-extensions::messages.medium_not_found')
//            );
        }

        $component = new LabPreviewBase(
            id: $baseId,
            media: $medium,
            options: $options,
            dataSource: $dataSource,
        );
        $html = Blade::renderComponent($component);
        $debugHtml = null;

        if (config('medialibrary-extensions.debug') && $request->boolean('include_debug')) {
            $debugComponent = new Debug(
                modelOrClassName: $modelType,
                config: $component->getConfig(),
                options: $options,
            );

            $debugHtml = Blade::renderComponent($debugComponent);
        }

        return response()->json([
            'html' => $html,
            'debugHtml' => $debugHtml,
            'success' => true,
            'target' => $baseId,
            'medium_id' => $medium->id,
        ]);
    }
}
