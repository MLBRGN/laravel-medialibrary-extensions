<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;

class StoreMultipleMediaAction
{
    public function __construct(
        protected StoreMultiplePermanentAction $storeMultiplePermanentAction,
        protected StoreMultipleTemporaryAction $storeMultipleTemporaryAction
    ) {}

    public function execute(MediaManagerUploadMultipleRequest $request): RedirectResponse|JsonResponse
    {
        if ($request->temporary_upload === 'true') {
            return $this->storeMultipleTemporaryAction->execute($request);
        }

        return $this->storeMultiplePermanentAction->execute($request);
    }
}
