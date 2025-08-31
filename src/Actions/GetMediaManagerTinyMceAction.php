<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerTinyMceRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest;

class GetMediaManagerTinyMceAction
{
    public function __construct(
        protected GetMediaManagerTinyMcePermanentAction $getMediaManagerTinyMcePermanentAction,
        protected GetMediaManagerTinyMceTemporaryAction $getMediaManagerTinyMceTemporaryAction,
    ) {}

    public function execute(GetMediaManagerTinyMceRequest $request): View
    {

        if ($request->temporary_uploads === 'true') {

            return $this->getMediaManagerTinyMceTemporaryAction->execute($request);
        }

        return $this->getMediaManagerTinyMcePermanentAction->execute($request);
    }
}
