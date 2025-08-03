<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerTemporaryUploadDestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

class DeleteTemporaryUploadAction
{
    public function execute(MediaManagerTemporaryUploadDestroyRequest $request, TemporaryUpload $temporaryUpload): JsonResponse|RedirectResponse
    {
        $initiatorId = $request->initiator_id;
        $temporaryUpload->delete();

        return MediaResponse::success($request, $initiatorId, __('media-library-extensions::messages.medium_removed'));
    }
}
