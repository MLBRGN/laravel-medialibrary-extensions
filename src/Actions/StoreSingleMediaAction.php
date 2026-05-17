<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;

class StoreSingleMediaAction
{
    public function __construct(
        protected StoreSinglePermanentAction $storeSinglePermanentAction,
        protected StoreSingleTemporaryAction $storeSingleTemporaryAction
    ) {}

    public function execute(StoreSingleRequest $request): RedirectResponse|JsonResponse
    {
        if ($request->temporary_upload_mode === 'true') {
            return $this->storeSingleTemporaryAction->execute($request);
        }

        return $this->storeSinglePermanentAction->execute($request);
    }
}
