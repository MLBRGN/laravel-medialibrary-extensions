<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\View\Components\Preview\MediaPreviews;
use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\Debug;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GetMediaPreviewerTemporaryHTMLAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    /**
     * @throws Exception
     */
    public function execute(GetMediaManagerPreviewerHTMLRequest $request): JsonResponse|Response
    {
        $dataSource = $request->input('data_source');
        $initiatorId = $request->input('initiator_id');
        $instanceId = $request->input('instance_id') ?? '';
        $modelType = $request->input('model_type');
        // no modelId
        $singleMediaId = $request->input('single_medium_id');
        $singleMediaId = ($singleMediaId !== 'null' && $singleMediaId !== null && $singleMediaId !== '') ? (int) $singleMediaId : null;
        $multiple = $request->boolean('multiple');
        $disabled = $request->boolean('disabled');
        $readonly = $request->boolean('readonly');
        $selectable = $request->boolean('selectable');
        $theme = $request->input('theme');

        $options = json_decode($request->input('options'), true) ?? [];
        $options['temporaryUploadMode'] = true;

        if ($theme) {
            //            $options['theme'] = $theme;
            $options['frontendTheme'] = $theme;
        }

        $collections = json_decode($request->input('collections'), true) ?? [];
        $model = new $modelType;

        $collections = collect($collections)
            ->filter(fn ($collection) => ! empty($collection))
            ->values()
            ->all();

        $singleMedia = null;
        $totalMediaCount = 0;

        $temporaryUploadModel = $this->mediaService->make(
            TemporaryUpload::class,
            $dataSource
        );

        $sessionId = $request->hasSession() ? $request->session()->getId() : session()->getId();

        // counting media
        if ($singleMediaId !== null) {
            Log::info('singleMediaId', [$singleMediaId]);

            $singleMedia = $temporaryUploadModel
                ->newQuery()
                ->find($singleMediaId);

            if ($singleMedia) {
                $totalMediaCount = 1;
            } else {
                throw new Exception(__('medialibrary-extensions::messages.medium_not_found'));
            }
        } else {
            Log::info('multiple', [$multiple]);
            // Count all media in collections
            foreach ($collections as $collectionName) {
                $totalMediaCount += TemporaryUpload::getForCurrentSession(
                    $collectionName,
                    $instanceId,
                    $dataSource,
                    $sessionId
                )->count();
            }
        }

        $component = new MediaPreviews(
            id: $initiatorId,
            modelOrClassName: $modelType,
            collections: $collections,
            options: $options,
            singleMedia: $singleMedia,
            multiple: $multiple,
            disabled: $disabled,
            readonly: $readonly,
            selectable: $selectable,
            instanceId: $instanceId,
            dataSource: $dataSource,
            sessionId: $sessionId,
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
            'mediaCount' => $totalMediaCount,
            'success' => true,
            'instanceId' => $instanceId,
            'dataSource' => $dataSource,
            'target' => $initiatorId, // TODO contains old id, but this is probably what i want
        ]);
    }
}
