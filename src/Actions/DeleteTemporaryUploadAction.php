<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerDestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerTemporaryUploadDestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DeleteTemporaryUploadAction
{
    public function execute(MediaManagerTemporaryUploadDestroyRequest $request, TemporaryUpload $temporaryUpload): JsonResponse|RedirectResponse
    {
        $initiatorId = $request->initiator_id;

//        dd('trying to delete temporary upload');
        // $this->authorize('deleteMedia', $media); // Authorization can be handled in the controller or via policies

//        if (config('media-library-extensions.demo_mode')) {
//            $temporaryUpload->setConnection('media_demo');
//        }

//        Log::info('Media connection:', [
//            'model' => get_class($media),
//            'conn' => $media->getConnectionName(),
//            'default' => config('database.default'),
//        ]);

        $temporaryUpload->delete();

        return MediaResponse::success($request, $initiatorId, __('media-library-extensions::messages.medium_removed'));
    }
}
