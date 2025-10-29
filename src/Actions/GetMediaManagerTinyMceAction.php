<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerTinyMceRequest;

class GetMediaManagerTinyMceAction
{
    public function __construct(
        protected GetMediaManagerTinyMcePermanentAction $getMediaManagerTinyMcePermanentAction,
        protected GetMediaManagerTinyMceTemporaryAction $getMediaManagerTinyMceTemporaryAction,
    ) {}

    public function execute(GetMediaManagerTinyMceRequest $request): View
    {

        $options = json_decode($request->string('options'), true);
        $temporaryUploadMode = $options['temporaryUploadMode'] === true;

        if ($temporaryUploadMode) {
            return $this->getMediaManagerTinyMceTemporaryAction->execute($request);
        }

        return $this->getMediaManagerTinyMcePermanentAction->execute($request);
    }
}
