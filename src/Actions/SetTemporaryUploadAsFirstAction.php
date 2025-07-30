<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Hoa\File\Temporary\Temporary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryUploadAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

class SetTemporaryUploadAsFirstAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(SetTemporaryUploadAsFirstRequest $request): JsonResponse|RedirectResponse
    {
        $initiatorId = $request->initiator_id;
        $collection = $request->target_media_collection;
        $mediumId = (int) $request->medium_id;

        $mediaItems = TemporaryUpload::where('session_id', $request->session()->getId())
            ->when($collection, fn($query) => $query->where('extra_properties->image_collection', $collection))
            ->orderBy('order_column')
            ->get();

        $orderedIds = $mediaItems->pluck('id')->toArray();

        // Move selected ID to the front
        $orderedIds = array_filter($orderedIds, fn ($id) => $id !== $mediumId);
        array_unshift($orderedIds, $mediumId);

        $this->setTemporaryUploadOrder($orderedIds);

        return MediaResponse::success(
            $request,
            $initiatorId,
            __('media-library-extensions::messages.medium_set_as_main')
        );
    }

    protected function setTemporaryUploadOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            TemporaryUpload::where('id', $id)->update(['order_column' => $index + 1]);
        }
    }
}
