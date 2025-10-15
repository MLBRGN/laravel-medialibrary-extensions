<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerPreview;

class GetMediaPreviewerTemporaryHTMLAction
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
        // no modelId

        // Decode JSON strings to arrays
        $options = json_decode($request->input('options'), true) ?? [];
        $options['temporaryUploads'] = true; // TODO should be in options already
        $collections = json_decode($request->input('collections'), true) ?? [];

        $collections = collect($collections)
            ->filter(fn ($collection) => ! empty($collection))
            ->all();

        $totalMediaCount = 0;

        foreach ($collections as $collectionName) {
            $totalMediaCount += TemporaryUpload::forCurrentSession($collectionName)->count();
        }

        $component = new MediaManagerPreview(
            id: $initiatorId,
            modelOrClassName: $modelType,
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
