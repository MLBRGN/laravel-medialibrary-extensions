<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest;

class GetMediaPreviewerHTMLAction
{
    public function __construct(
        protected GetMediaPreviewerPermanentHTMLAction $getMediaPreviewerPermanentHTMLAction,
        protected GetMediaPreviewerTemporaryHTMLAction $getMediaPreviewerTemporaryHTMLAction,
    ) {}

    public function execute(GetMediaPreviewerHTMLRequest $request): JsonResponse|Response
    {
        if ($request->temporary_uploads === 'true') {
            return $this->getMediaPreviewerTemporaryHTMLAction->execute($request);
        }

        return $this->getMediaPreviewerPermanentHTMLAction->execute($request);
    }
}
