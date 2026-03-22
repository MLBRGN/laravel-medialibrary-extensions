<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest;

class GetMediaManagerPreviewerHTMLAction
{
    public function __construct(
        protected GetMediaPreviewerPermanentHTMLAction $getMediaPreviewerPermanentHTMLAction,
        protected GetMediaPreviewerTemporaryHTMLAction $getMediaPreviewerTemporaryHTMLAction,
    ) {}

    public function execute(GetMediaManagerPreviewerHTMLRequest $request): JsonResponse|Response
    {
        if ($request->temporary_upload_mode === 'true') {
            return $this->getMediaPreviewerTemporaryHTMLAction->execute($request);
        }

        return $this->getMediaPreviewerPermanentHTMLAction->execute($request);
    }
}
