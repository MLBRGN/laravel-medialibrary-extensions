<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerDestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadYouTubeRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerPreview;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerController extends Controller
{
    protected function getModel(string $modelType, string $modelId): ?HasMedia
    {
        abort_if(! class_exists($modelType), 400, 'Invalid model type');

        return (new $modelType())::findOrFail($modelId);
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function store(MediaManagerUploadSingleRequest $request): RedirectResponse|JsonResponse
    {

        $model = $this->getModel($request->model_type, $request->model_id);
//        $this->authorize('uploadMedia', Media::class);

        $initiatorId = $request->initiator_id;
        $imageCollectionName = $request->image_collection;
        $documentCollectionName = $request->document_collection;
        $field = config('media-library-extensions.upload_field_name_single');

        $file = $request->file($field);

        Log::info('Media connection:', [
            'model' => get_class($model),
            'conn' => $model->getConnectionName(),
            'default' => config('database.default'),
        ]);

        if ($file) {
            $mimeType = $file->getMimeType();
            if (in_array($mimeType, config('media-library-extensions.allowed_mimetypes.image'))) {
                $collection = $imageCollectionName;
            } elseif (in_array($mimeType, config('media-library-extensions.allowed_mimetypes.document'))) {
                $collection = $documentCollectionName;
            } else {
                // early return when one of the files is not supported
                return $this->respondWithStatus(
                    $request,
                    $initiatorId,
                    'error',
                    __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype'),
                    $initiatorId
                );
            }

            $model
                ->addMedia($request->file($field))
                ->toMediaCollection($collection);

            return $this->respondWithStatus(
                $request,
                $initiatorId,
                'success',
                __('media-library-extensions::messages.upload_success'),
                $initiatorId
            );
        }
        return $this->respondWithStatus(
            $request,
            $initiatorId,
            'error',
            __('media-library-extensions::messages.upload_no_file'),
            $initiatorId
        );
    }

    /**
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function storeMany(MediaManagerUploadMultipleRequest $request): RedirectResponse|JsonResponse
    {

        $model = $this->getModel($request->model_type, $request->model_id);
//        $this->authorize('uploadMedia', Media::class);

        $initiatorId = $request->initiator_id;
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
                    return $this->respondWithStatus(
                        $request,
                        $initiatorId,
                        'error',
                        __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype'),
                        $initiatorId
                    );
                }

                $model->addMedia($file)
                    ->toMediaCollection($collection);
            }

            return $this->respondWithStatus(
                $request,
                $initiatorId,
                'success',
                __('media-library-extensions::messages.upload_success'),
                $initiatorId
            );

        }

        if ($request->filled('youtube_url')) {

            $videoId = extractYouTubeId($request->input('youtube_url'));

            // TODO
//            if (! $videoId) {
//                abort(422, 'Invalid YouTube URL');
//            }

            $thumbnailUrl = "https://img.youtube.com/vi/$videoId/maxresdefault.jpg";

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

        return $this->respondWithStatus(
            $request,
            $initiatorId,
            'error',
            __('media-library-extensions::messages.upload_no_files'),
            $initiatorId
        );

    }

    public function storeYouTube(MediaManagerUploadYouTubeRequest $request): RedirectResponse|JsonResponse
    {
        if(!config('media-library-extensions.youtube_support_enabled')) {
            abort(403);
        }

        $model = $this->getModel($request->model_type, $request->model_id);
//        $this->authorize('uploadMedia', Media::class);

        $initiatorId = $request->initiator_id;
        $collectionName = $request->collection_name;
        $field = config('media-library-extensions.upload_field_name_youtube');

        if ($request->filled($field)) {

            $videoId = extractYouTubeId($request->input($field));

            // TODO
//            if (! $videoId) {
//                abort(422, 'Invalid YouTube URL');
//            }

            $thumbnailUrl = "https://img.youtube.com/vi/$videoId/maxresdefault.jpg";

            /** @var HasMedia $model */
            $model
                ->addMediaFromUrl($thumbnailUrl)
                ->usingFileName('youtube-thumbnail-'.$videoId.'.jpg')
                ->withCustomProperties([
                    'youtube-url' => $request->input('youtube_url'),
                    'youtube-id' => $videoId,
                ])
                ->toMediaCollection($collectionName);

            return $this->respondWithStatus(
                $request,
                $initiatorId,
                'success',
                __('media-library-extensions::messages.youtube_video_uploaded'),
                $initiatorId
            );
        }

        return $this->respondWithStatus(
            $request,
            $initiatorId,
            'error',
            __('media-library-extensions::messages.upload_no_youtube_url'),
            $initiatorId
        );
    }

    public function destroy(MediaManagerDestroyRequest $request, Media $media): RedirectResponse|JsonResponse
    {
        $initiatorId = $request->initiator_id;

//        $this->authorize('deleteMedia', $media);

        if (config('media-library-extensions.demo_mode')) {
            $media->setConnection('media_demo');
        }

        Log::info('Media connection:', [
            'model' => get_class($media),
            'conn' => $media->getConnectionName(),
            'default' => config('database.default'),
        ]);

        $media->delete();

        return $this->respondWithStatus(
            $request,
            $initiatorId,
            'success',
            __('media-library-extensions::messages.medium_removed'),
            $initiatorId
        );

    }

    public function setAsFirst(SetAsFirstRequest $request): RedirectResponse|JsonResponse
    {
        $model = $this->getModel($request->model_type, $request->model_id);
//        $this->authorize('reorderMedia', Media::class);
        $targetMediaCollection = $request->target_media_collection;
        $initiatorId = $request->initiator_id;
        $mediumId = (int) $request->medium_id;

        $media = $model->getMedia($targetMediaCollection);

        Log::info('set as first Media connection:', [
            'model' => get_class($model),
            'conn' => $model->getConnectionName(),
            'default' => config('database.default'),
        ]);

        // Reorder media so the selected medium is first
        $orderedIds = $media->pluck('id')->toArray();
        $orderedIds = array_filter($orderedIds, fn ($id) => $id !== $mediumId);
        array_unshift($orderedIds, $mediumId);

        if (config('media-library-extensions.demo_mode')) {
            $originalConnection = config('database.default');
            config(['database.default' => 'media_demo']);
            Media::setNewOrder($orderedIds);
            config(['database.default' => $originalConnection]);
        } else {
            Media::setNewOrder($orderedIds);
        }

        return $this->respondWithStatus(
            $request,
            $initiatorId,
            'success',
            __('media-library-extensions::messages.medium_set_as_main'),
            $initiatorId
        );
    }

    // used by ajax to refresh previews of images after upload / delete / new order
    public function getMediaPreviewerHTML(GetMediaPreviewerHTMLRequest $request): Response|JsonResponse
    {
        $initiatorId = $request->input('initiator_id');
        $id = $initiatorId;

        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        $model = $this->getModel($modelType, $modelId);

        $imageCollection = $request->input('image_collection');
        $documentCollection = $request->input('document_collection');
        $youtubeCollection = $request->input('youtube_collection');

        $frontendTheme = $request->input('frontend_theme');

        $destroyEnabled = $request->input('destroy_enabled');
        $setAsFirstEnabled = $request->input('set_as_first_enabled');
        $showMediaUrl = $request->input('show_media_url');
        $showOrder = $request->input('show_order');

        $component = new MediaManagerPreview(
            id: $id,

            model: $model,

            imageCollection: $imageCollection,
            documentCollection: $documentCollection,
            youtubeCollection: $youtubeCollection,

            frontendTheme: $frontendTheme,

            destroyEnabled: $destroyEnabled,
            setAsFirstEnabled: $setAsFirstEnabled,
            showMediaUrl: $showMediaUrl,
            showOrder: $showOrder,
        );

        $html = Blade::renderComponent($component);

        return response()->json([
            'html' => $html,
            'success' => true,
            'target' => $initiatorId,
        ]);
    }

    protected function respondWithStatus(Request $request, string $initiatorId, string $type, string $message, ?string $fragmentIdentifier = null): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'initiator_id' => $initiatorId,
                'type' => $type,
                'message' => $message,
            ]);
        }

        $redirect = redirect()->back()
            ->with(status_session_prefix(), [
                'initiator_id' => $initiatorId,
                'type' => $type,
                'message' => $message,
            ]);

        if ($fragmentIdentifier) {
            $redirect = $redirect->withFragment($fragmentIdentifier);
        }

        return $redirect;
    }

}
