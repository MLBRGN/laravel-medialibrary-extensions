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
use Mlbrgn\MediaLibraryExtensions\View\Components\Preview\MediaPreviews;
use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\Debug;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GetMediaPreviewerTemporaryHTMLAction
{
    public ?bool $temporaryUploadMode = true;

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
        if ($request->has('temporary_upload_mode')) {
            $this->temporaryUploadMode = $request->boolean('temporary_upload_mode');
            $options['temporaryUploadMode'] = $this->temporaryUploadMode;
        } else {
            $options['temporaryUploadMode'] = true;
            $this->temporaryUploadMode = true;
        }

        if ($theme) {
            //            $options['theme'] = $theme;
            $options['frontendTheme'] = $theme;
        }

        $collections = json_decode($request->input('collections'), true) ?? [];

        $sessionId = ($request->hasSession() ? $request->session()->getId() : session()->getId());
        $model = new $modelType;

        $collections = collect($collections)
            ->filter(fn ($collection) => ! empty($collection))
            ->values()
            ->all();

        $singleMedia = null;
        $totalMediaCount = 0;

        $sessionId = $request->input('session_id') ?? ($request->hasSession() ? $request->session()->getId() : session()->getId());

        if ($singleMediaId !== null) {
            $singleMedia = $this->mediaService->findTemporaryUpload($singleMediaId, $dataSource);

            if ($singleMedia) {
                $totalMediaCount = 1;
            } else {
                throw new Exception(__('medialibrary-extensions::messages.medium_not_found'));
            }
        } else {
//            Log::info('GetTempHtmlAction collections: '.print_r($collections, true));
//            Log::info('GetTempHtmlAction instanceID: '.$instanceId);
//            Log::info('GetTempHtmlAction dataSource: '.$dataSource);
//            Log::info('GetTempHtmlAction sessionId: '.$sessionId);
            $totalMediaCount = $this->mediaService->countTemporaryUploadsInCollections(
                $collections,
                $instanceId,
                $sessionId,
                $dataSource
            );
        }

        $component = new MediaPreviews(
            id: $initiatorId,
            mediaManagerId: $initiatorId, // Action uses the initiator_id (DOM id) as the base identity for logical operations here
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
