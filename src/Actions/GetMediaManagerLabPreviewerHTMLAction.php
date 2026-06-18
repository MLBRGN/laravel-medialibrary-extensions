<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerLabPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\View\Components\Lab\LabPreviewBase;
use Mlbrgn\MediaLibraryExtensions\View\Components\Lab\LabPreviewOriginal;
use Mlbrgn\MediaLibraryExtensions\View\Components\Lab\LabPreviews;
use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\Debug;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GetMediaManagerLabPreviewerHTMLAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    /**
     * @throws Exception
     */
    public function execute(GetMediaManagerLabPreviewerHTMLRequest $request): JsonResponse|Response
    {
//        Log::info('GetMediaManagerLabPreviewerHTMLAction invoked');
        $initiatorId = $request->input('initiator_id');
        $mediumId = $request->get('medium_id');
        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        $theme = $request->input('theme');
        $part = $request->get('part', 'all');
        $options = json_decode($request->input('options'), true) ?? [];
        $dataSource = $request->input('data_source');

        if ($theme) {
            $options['theme'] = $theme;
        }

        $model = $this->mediaService->findMediaModel($modelType, $modelId, $dataSource);

        // have to query the model, don't use Media directly (this uses wrong db for demo pages)
        $medium = $model->media()->findOrFail($mediumId);

        switch ($part) {

            case 'original':
                $component = new LabPreviewOriginal(
                    id: $initiatorId,
                    media: $medium,
                    options: $options
                );
                break;
            case 'base':
                $component = new LabPreviewBase(
                    id: $initiatorId,
                    media: $medium,
                    options: $options
                );
                break;
            default:
                $component = new LabPreviews(
                    id: $initiatorId,
                    media: $medium,
                    options: $options
                );
                break;
        }

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
            'target' => $initiatorId,
            'medium_id' => $medium->id,
            'part' => $part,
        ]);
    }
}
