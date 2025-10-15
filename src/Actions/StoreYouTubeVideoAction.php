<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;

class StoreYouTubeVideoAction
{
    public function __construct(
        protected StoreYouTubeVideoPermanentAction $storeYouTubeVideoPermanentAction,
        protected StoreYouTubeVideoTemporaryAction $storeYouTubeVideoTemporaryAction,
    ) {}

    public function execute(StoreYouTubeVideoRequest $request): RedirectResponse|JsonResponse
    {
        if (! config('media-library-extensions.youtube_support_enabled')) {
            abort(403);
        }

        if ($request->boolean('temporary_upload_mode')) {
            return $this->storeYouTubeVideoTemporaryAction->execute($request);
        }

        return $this->storeYouTubeVideoPermanentAction->execute($request);
    }
}
