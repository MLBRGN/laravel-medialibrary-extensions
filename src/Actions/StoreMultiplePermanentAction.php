<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\YouTubeService;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class StoreMultiplePermanentAction
{
    use ChecksMediaLimits;

    public function __construct(
        protected MediaService $mediaService,
        protected YouTubeService $youTubeService
    ) {
    }

    public function execute(MediaManagerUploadMultipleRequest $request): RedirectResponse|JsonResponse
    {
        $model = $this->mediaService->resolveModel($request->model_type, $request->model_id);
        $initiatorId = $request->initiator_id;
        $field = config('media-library-extensions.upload_field_name_multiple');
        $files = $request->file($field);

        if (empty($files)) {
            return MediaResponse::error($request, $initiatorId,
                __('media-library-extensions::messages.upload_no_files'));
        }

        $collections = collect([
            $request->input('image_collection'),
            $request->input('document_collection'),
            $request->input('youtube_collection'),
            $request->input('video_collection'),
            $request->input('audio_collection'),
        ])->filter()->all();// remove falsy values

        $maxItemsInCollection = config('media-library-extensions.max_items_in_collection');
        if ($this->countModelMediaInCollections($model, $collections) >= $maxItemsInCollection) {
            return MediaResponse::error(
                $request,
                $request->initiator_id,
                __('media-library-extensions::messages.this_collection_can_contain_up_to_:items_items', [
                    'items' => $maxItemsInCollection
                ])
            );
        }

        // Determine priority for this file
        $currentMaxPriority = $model->getMedia()
            ->filter(fn($m) => in_array($m->collection_name, $collections))
            ->max(fn($m) => $m->getCustomProperty('priority', 0));

        foreach ($files as $file) {
            $collection = $this->mediaService->determineCollection($file);

            if (!$collection) {
                return MediaResponse::error(
                    $request, $initiatorId,
                    __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype')
                );
            }

            try {
                $model->addMedia($file)
                    ->withCustomProperties([
                        'priority' => $currentMaxPriority++
                    ])
                    ->toMediaCollection($collection);
            } catch (Exception $e) {
                Log::error($e);
                return MediaResponse::error(
                    $request,
                    $request->initiator_id,
                    __('media-library-extensions::messages.something_went_wrong')
                );
            }
        }

        return MediaResponse::success(
            $request, $initiatorId,
            __('media-library-extensions::messages.upload_success')
        );

    }
}
