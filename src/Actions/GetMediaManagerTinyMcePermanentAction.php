<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\Response;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerTinyMceRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

class GetMediaManagerTinyMcePermanentAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(GetMediaManagerTinyMceRequest $request): View
    {

        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        $id = (string) $request->input('base_id');
        $multiple = $request->boolean('multiple');
        $collections = json_decode(request()->string('collections'), true);
        $options = json_decode(request()->string('options'), true);
        $dataSource = $request->input('data_source', 'default');// TODO in demo should be demo_default

        $model = null;

        if ($modelType && $modelId) {
            try {
                $model = $this->mediaService->resolveModelById($modelType, $modelId, $dataSource);
            } catch (\Exception $e) {
                return view('medialibrary-extensions::errors.error', [
                    'title' => __('medialibrary-extensions::messages.something_went_wrong'),
                    'message' => __('medialibrary-extensions::messages.medium_not_found') . ' ' . __('medialibrary-extensions::messages.could_not_load_file_picker'),
                    'details' => [
                        'Model' => $modelType,
                        'ID' => $modelId,
                        'dataSource' => $dataSource,
                    ],
                ]);
            }
        }

        $modelOrClassName = $model ?? $modelType;

        return view('medialibrary-extensions::media-manager-tinymce-wrapper', [
            'id' => $id,
            'modelOrClassName' => $modelOrClassName,
            'multiple' => $multiple,
            'collections' => $collections,
            'options' => $options,
            'dataSource' => $dataSource,
        ]);
    }
}
