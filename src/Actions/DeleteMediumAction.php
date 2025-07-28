<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerDestroyRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DeleteMediumAction
{
    public function execute(MediaManagerDestroyRequest $request, Media $media): JsonResponse|RedirectResponse
    {
        $initiatorId = $request->initiator_id;

        // $this->authorize('deleteMedia', $media); // Authorization can be handled in the controller or via policies

        if (config('media-library-extensions.demo_mode')) {
            $media->setConnection('media_demo');
        }

        Log::info('Media connection:', [
            'model' => get_class($media),
            'conn' => $media->getConnectionName(),
            'default' => config('database.default'),
        ]);

        $media->delete();

        return MediaResponse::success($request, $initiatorId, __('media-library-extensions::messages.medium_removed'));
    }
}
