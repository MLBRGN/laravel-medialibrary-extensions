<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

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
        $id = 'something_for_now'; // TODO
        $multiple = true; // TODO
        $collections = json_decode(request()->string('collections'), true);
        $options = json_decode(request()->string('options'), true);

        $model = null;
        if ($modelType && $modelId) {
            $model = $modelType::findOrFail($modelId);
        }
        $modelOrClassName = $model ?? $modelType;

        return view('media-library-extensions::media-manager-tinymce-wrapper', [
            'id' => $id,
            'modelOrClassName' => $modelOrClassName,
            'multiple' => $multiple,
            'collections' => $collections,
            'options' => $options,
        ]);
    }
}
