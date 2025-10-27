<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerTinyMceRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Spatie\MediaLibrary\HasMedia;

class GetMediaManagerTinyMcePermanentAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(GetMediaManagerTinyMceRequest $request): View
    {

//        $options = json_decode($request->string('options'), true);

//        $initiatorId = $request->string('initiator_id');
//        $model = $this->mediaService->resolveModel(
//            $request->string('model_type'),
//            $request->string('model_id'),
//        );

        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        $id = 'something_for_now'; // TODO
        $multiple = false;
        $collections = json_decode(request()->string('collections'), true);
        $options = json_decode(request()->string('options'), true);

        $model = null;
        if ($modelType && $modelId) {
            $model = $modelType::findOrFail($modelId);
        }
        $modelOrClassName = $model ?? $modelType;

//        dd([
//            'id' => $id,
//            'modelOrClassName' => $modelOrClassName,
//            'multiple' => $multiple,
//            'collections' => $collections,
//            'options' => $options,
//        ]);
        return view('media-library-extensions::media-manager-tinymce-wrapper', [
            'id' => $id,
            'modelOrClassName' => $modelOrClassName,
            'multiple' => $multiple,
            'collections' => $collections,
            'options' => $options,
        ]);
//        $frontendTheme = $options('frontendTheme') ? $options('frontendTheme') : config('media-library-extensions.frontend_theme', 'bootstrap-5');
//        $temporaryUploadMode = $options['frontendTheme'] ?? false;
//        $multiple = $request->boolean('multiple');
//        $showSetAsFirstButton = $options('showSetAsFirstButton');
//
//        if ($modelOrClassName instanceof HasMedia) {
//            $model = $modelOrClassName;
//            $modelType = $modelOrClassName->getMorphClass();
//            $modelId = $modelOrClassName->getKey();
//        } elseif (is_string($modelOrClassName)) {
//            if (! class_exists($modelOrClassName)) {
//                throw new \InvalidArgumentException(__('media-library-extensions::messages.class_does_not_exist', ['class_name' => $modelOrClassName]));
//            }
//
//            if (! is_subclass_of($modelOrClassName, HasMedia::class)) {
//                throw new \InvalidArgumentException(__('media-library-extensions::messages.class_must_implement', ['class_name' => HasMedia::class]));
//            }
//
//            $model = null;
//            $modelType = $modelOrClassName;
//            $modelId = null;
//            $temporaryUploadMode = true;
//        }

//        return view('media-library-extensions::media-manager-tinymce', compact('initiatorId', 'model', 'temporaryUpload', 'id'));

    }
}
