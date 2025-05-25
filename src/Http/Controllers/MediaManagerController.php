<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\SpatieMediaLibraryExtensions\Http\Requests\MediaManagerDestroyRequest;
use Mlbrgn\SpatieMediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;
use Mlbrgn\SpatieMediaLibraryExtensions\Http\Requests\MediaManagerUploadSingleRequest;
use Mlbrgn\SpatieMediaLibraryExtensions\Http\Requests\SetAsFirstRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerController extends Controller
{
    protected function getModel(string $modelType, string $modelId): ?Model
    {
        abort_if(! class_exists($modelType), 400, 'Invalid model type');

        return (new $modelType)::findOrFail($modelId);
    }

    protected function redirectBackWithStatus(string $targetId, string $type, string $message, ?string $fragment = null): RedirectResponse
    {
        $redirect = redirect()->back()
            ->with(status_session_prefix('status'), [
                'target' => $targetId,
                'type' => $type,
                'message' => $message,
            ]);

        if ($fragment) {
            $redirect = $redirect->withFragment($fragment);
        }

        return $redirect;
    }

    public function store(MediaManagerUploadSingleRequest $request): RedirectResponse
    {

        $model = $this->getModel($request->model_type, $request->model_id);
        $this->authorize('uploadMedia', Media::class);

        $targetId = $request->target_id;
        $collectionName = $request->collection_name;

        if ($request->hasFile('medium')) {
            $model->addMedia($request->medium)->toMediaCollection($collectionName);

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
            __('media-library-extensions::messages.upload-no-files'),
            $targetId
        );
    }

    public function storeMany(MediaManagerUploadMultipleRequest $request): RedirectResponse
    {
        $model = $this->getModel($request->model_type, $request->model_id);
        $this->authorize('uploadMedia', Media::class);

        $targetId = $request->target_id;
        $collectionName = $request->collection_name;

        if ($request->hasFile('media')) {
            foreach ($request->media as $file) {
                $model->addMedia($file)->toMediaCollection($collectionName);
            }

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
            __('media-library-extensions::messages.upload-no-files'),
            $targetId
        );
    }

    public function destroy(MediaManagerDestroyRequest $request, Media $media): RedirectResponse
    {
        $targetId = $request->target_id;

        if ($media) {
            $this->authorize('deleteMedia', $media);
            $media->delete();

            return $this->redirectBackWithStatus(
                $targetId,
                'success',
                __('media-library-extensions::messages.medium-removed'),
                $targetId
            );
        }

        return $this->redirectBackWithStatus(
            $targetId,
            'error',
            __('media-library-extensions::messages.medium-could-not-be-removed'),
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
            __('media-library-extensions::messages.medium-set-as-main'),
            $targetId
        );
    }
}
