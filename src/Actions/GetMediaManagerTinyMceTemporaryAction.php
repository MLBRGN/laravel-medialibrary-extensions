<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerTinyMceRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

class GetMediaManagerTinyMceTemporaryAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(GetMediaManagerTinyMceRequest $request): View
    {
        $initiatorId = $request->input('initiator_id');
        $model = $this->mediaService->resolveModel(
            $request->input('model_type'),
            $request->input('model_id'),
        );

        return view('media-library-extensions::media-manager-tinymce', compact('initiatorId', 'model'));

    }
}
