<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerTinyMceRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerTinymce;
use Spatie\MediaLibrary\HasMedia;

class GetMediaManagerTinyMcePermanentAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}


    public function execute(GetMediaManagerTinyMceRequest $request): View
    {
        $initiatorId = 'something_for_now';
        $model = $this->mediaService->resolveModel(
            $request->input('model_type'),
            $request->input('model_id'),
        );

        $frontendTheme = $request->input('frontend_theme') ? $request->input('frontend_theme')  : config('medialibrary-extensions.frontend_theme', 'bootstrap-5');
        $modelOrClassName = $request->input('model_or_class_name');
        $model = null;
        $modelType = null;
        $modelId = null;
        $temporaryUpload = null;
        $multiple = false;
        $showSetAsFirstButton = true;

        if ($modelOrClassName instanceof HasMedia) {
            $model = $modelOrClassName;
            $modelType = $modelOrClassName->getMorphClass();
            $modelId = $modelOrClassName->getKey();
        } elseif (is_string($modelOrClassName)) {
            if (! class_exists($modelOrClassName)) {
                throw new \InvalidArgumentException(__('media-library-extensions::messages.class_does_not_exist', ['class_name' => $modelOrClassName]));
            }

            if (! is_subclass_of($modelOrClassName, HasMedia::class)) {
                throw new \InvalidArgumentException(__('media-library-extensions::messages.class_must_implement', ['class_name' => HasMedia::class]));
            }

            $model = null;
            $modelType = $modelOrClassName;
            $modelId = null;
            $temporaryUpload = true;
        }



        return view('media-library-extensions::media-manager-tinymce', compact('initiatorId', 'model', 'temporaryUpload', 'id'));

    }

}
