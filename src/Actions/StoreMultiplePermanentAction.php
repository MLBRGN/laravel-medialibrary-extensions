<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class StoreMultiplePermanentAction
{
    use ChecksMediaLimits;

    public function __construct(
        protected MediaService $mediaService,
    ) {}

    public function execute(StoreMultipleRequest $request): RedirectResponse|JsonResponse
    {
        $model = $this->mediaService->resolveModel($request->model_type, $request->model_id);
        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id; // non-xhr needs media-manager-id, xhr relies on initiatorId

        $field = config('media-library-extensions.upload_field_name_multiple');
        $files = $request->file($field);

        if (empty($files)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.upload_no_files')
            );
        }

        $collections = collect([
            $request->input('image_collection'),
            $request->input('document_collection'),
            $request->input('youtube_collection'),
            $request->input('video_collection'),
            $request->input('audio_collection'),
        ])->filter()->all(); // remove falsy values

        $maxItemsInCollection = config('media-library-extensions.max_items_in_shared_media_collections');
        $mediaInCollections = $this->countModelMediaInCollections($model, $collections);
        $nextPriority = $mediaInCollections;

        if ($mediaInCollections >= $maxItemsInCollection) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.this_collection_can_contain_up_to_:items_items', [
                    'items' => $maxItemsInCollection,
                ])
            );
        }
        $successCount = 0;
        $errors = [];

        foreach ($files as $file) {
            $collection = $this->mediaService->determineCollection($file);

            if (! $collection) {
                $errors[] = $file->getClientOriginalName();

                continue; // skip invalid mimetype but continue with others
            }

            try {
                $model->addMedia($file)
                    ->withCustomProperties([
                        'priority' => $nextPriority,
                    ])
                    ->toMediaCollection($collection);
                $nextPriority++;
                $successCount++;
            } catch (Exception $e) {
                Log::error($e);
                $errors[] = $file->getClientOriginalName();
            }
        }

        // Return appropriate response
        if ($successCount === 0) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.upload_failed')
            );
        }

        $message = __('media-library-extensions::messages.upload_success');
        if (! empty($errors)) {
            $message .= ' '.__('media-library-extensions::messages.some_uploads_failed', [
                'files' => implode(', ', $errors),
            ]);
        }

        return MediaResponse::success(
            $request,
            $initiatorId,
            $mediaManagerId,
            $message);

    }
}
