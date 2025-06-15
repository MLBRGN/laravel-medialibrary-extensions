<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerDestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadYouTubeRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetAsFirstRequest;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerController extends Controller
{
    protected function getModel(string $modelType, string $modelId): ?HasMedia
    {
        abort_if(! class_exists($modelType), 400, 'Invalid model type');

        return (new $modelType)::findOrFail($modelId);
    }

    protected function redirectBackWithStatus(string $targetId, string $type, string $message, ?string $fragment = null): RedirectResponse
    {
        $redirect = redirect()->back()
            ->with(status_session_prefix(), [
                'target' => $targetId,
                'type' => $type,
                'message' => $message,
            ]);

        if ($fragment) {
            $redirect = $redirect->withFragment($fragment);
        }

        return $redirect;
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function store(MediaManagerUploadSingleRequest $request): RedirectResponse
    {

        $model = $this->getModel($request->model_type, $request->model_id);
        $this->authorize('uploadMedia', Media::class);

        $targetId = $request->target_id;
//        $collectionName = $request->collection_name;
        $imageCollectionName = $request->image_collection;
        $documentCollectionName = $request->document_collection;
        $field = config('media-library-extensions.upload_field_name_single');

        $file = $request->file($field);

        if ($file) {
            $mimeType = $file->getMimeType();
            if (in_array($mimeType, config('media-library-extensions.allowed_mimetypes.image'))) {
                $collection = $imageCollectionName;
            } elseif (in_array($mimeType, config('media-library-extensions.allowed_mimetypes.document'))) {
                $collection = $documentCollectionName;
            } else {
                // early return when one of the files is not supported
                return $this->redirectBackWithStatus(
                    $targetId,
                    'error',
                    __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype'),
                    $targetId
                );
            }

            $model
                ->addMedia($request->file($field))
                ->toMediaCollection($collection);

            return $this->redirectBackWithStatus(
                $targetId,
                'success',
                __('media-library-extensions::messages.upload_success'),
                $targetId
            );
        }

        return $this->redirectBackWithStatus(
            $targetId,
            'error',
            __('media-library-extensions::messages.upload_no_files'),
            $targetId
        );
    }

    /**
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function storeMany(MediaManagerUploadMultipleRequest $request): RedirectResponse
    {

        $model = $this->getModel($request->model_type, $request->model_id);
        $this->authorize('uploadMedia', Media::class);

        $targetId = $request->target_id;
        $imageCollectionName = $request->image_collection;
        $documentCollectionName = $request->document_collection;

        $field = config('media-library-extensions.upload_field_name_multiple');

        if ($request->hasFile($field)) {

            foreach ($request->file($field) as $file) {
                $mimeType = $file->getMimeType();
                if (in_array($mimeType, config('media-library-extensions.allowed_mimetypes.image'))) {
                    $collection = $imageCollectionName;
                } elseif (in_array($mimeType, config('media-library-extensions.allowed_mimetypes.document'))) {
                    $collection = $documentCollectionName;
                } else {
                    // early return when one of the files is not supported
                    return $this->redirectBackWithStatus(
                        $targetId,
                        'error',
                        __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype'),
                        $targetId
                    );
                }

                $model->addMedia($file)
                    ->toMediaCollection($collection);
            }

            return $this->redirectBackWithStatus(
                $targetId,
                'success',
                __('media-library-extensions::messages.upload_success'),
                $targetId
            );
        }

        if ($request->filled('youtube_url')) {

            $videoId = extractYouTubeId($request->input('youtube_url'));

            // TODO
//            if (! $videoId) {
//                abort(422, 'Invalid YouTube URL');
//            }

            $thumbnailUrl = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";

            /** @var HasMedia $model */
            $model
                ->addMediaFromUrl($thumbnailUrl)
                ->usingFileName('youtube-thumbnail-'.$request->youtube_id.'.jpg')
                ->withCustomProperties([
                    'youtube-url' => $request->input('youtube_url'),
                    'youtube-id' => $videoId,
                ])
                ->toMediaCollection('workplace-youtube-videos');
        }


        return $this->redirectBackWithStatus(
            $targetId,
            'error',
            __('media-library-extensions::messages.upload_no_files'),
            $targetId
        );
    }

    public function storeYouTube(MediaManagerUploadYouTubeRequest $request): RedirectResponse
    {
        if(!config('media-library-extensions.youtube_support_enabled')) {
            abort(403);
        }

        $model = $this->getModel($request->model_type, $request->model_id);
        $this->authorize('uploadMedia', Media::class);

        $targetId = $request->target_id;
        $collectionName = $request->collection_name;
        $field = config('media-library-extensions.upload_field_name_youtube');

        if ($request->filled($field)) {

            $videoId = extractYouTubeId($request->input($field));

            // TODO
//            if (! $videoId) {
//                abort(422, 'Invalid YouTube URL');
//            }

            $thumbnailUrl = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";

            /** @var HasMedia $model */
            $model
                ->addMediaFromUrl($thumbnailUrl)
                ->usingFileName('youtube-thumbnail-'.$videoId.'.jpg')
                ->withCustomProperties([
                    'youtube-url' => $request->input('youtube_url'),
                    'youtube-id' => $videoId,
                ])
                ->toMediaCollection($collectionName);
        }


        return $this->redirectBackWithStatus(
            $targetId,
            'error',
            __('media-library-extensions::messages.upload_no_files'),
            $targetId
        );
    }

    public function destroy(MediaManagerDestroyRequest $request, Media $media): RedirectResponse
    {
        $targetId = $request->target_id;

        $this->authorize('deleteMedia', $media);
        $media->delete();

        return $this->redirectBackWithStatus(
            $targetId,
            'success',
            __('media-library-extensions::messages.medium_removed'),
            $targetId
        );

    }

    public function setAsFirst(SetAsFirstRequest $request): RedirectResponse
    {
        $model = $this->getModel($request->model_type, $request->model_id);
        $this->authorize('reorderMedia', Media::class);
        $collectionName = $request->collection_name;
        $targetId = $request->target_id;
        $mediumId = (int) $request->medium_id;

        $media = $model->getMedia($collectionName);

        // Reorder media so the selected medium is first
        $orderedIds = $media->pluck('id')->toArray();
        $orderedIds = array_filter($orderedIds, fn ($id) => $id !== $mediumId);
        array_unshift($orderedIds, $mediumId);
        Media::setNewOrder($orderedIds);

        return $this->redirectBackWithStatus(
            $targetId,
            'success',
            __('media-library-extensions::messages.medium_set_as_main'),
            $targetId
        );
    }
}
