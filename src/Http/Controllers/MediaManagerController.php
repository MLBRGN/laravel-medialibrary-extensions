<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Http\Controllers;

/** @noinspection PhpUnused */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\SpatieMediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;
use Mlbrgn\SpatieMediaLibraryExtensions\Http\Requests\MediaManagerUploadSingleRequest;
use Mlbrgn\SpatieMediaLibraryExtensions\Http\Requests\SetAsFirstRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class MediaManagerController
 *
 * This controller handles media management operations such as uploading single or multiple files,
 * deleting a medium, and setting a medium as the first in a collection.
 */
class MediaManagerController extends Controller
{
    protected function getModel(string $modelType, string $modelId): ?Model
    {

        abort_if(! class_exists($modelType), 400, 'Invalid model type');

        $model = (new $modelType)::findOrFail($modelId);
        //        $model = (new $modelType)::find($modelId); // Retrieve the model instance by ID
        //        abort_if(! $model, 400, 'Model not found');

        return $model;
    }

    public function store(MediaManagerUploadSingleRequest $request): RedirectResponse
    {
        // TODO check if correct implementation
        //        $this->authorize('uploadMedia', $model);
        //        if (! auth()->check()) {
        //            abort(403, __('media-library-extensions::messages.not-authorized'));
        //        }

        $modelType = $request->model_type;
        $modelId = $request->model_id;
        $collectionName = $request->collection_name;
        $model = $this->getModel($modelType, $modelId);

        $medium = $request->medium;

        if ($request->hasFile('medium')) {
            $model
                ->addMedia($medium)
                ->toMediaCollection($collectionName);
        } else {
            return back()
                ->with('error', __('media-library-extensions::messages.upload-no-files'));
        }

        return back()
            ->with('success', __('media-library-extensions::messages.upload_success'));
    }

    public function storeMany(MediaManagerUploadMultipleRequest $request): RedirectResponse
    {
        // TODO
        //        $this->authorize('uploadMedia', $model);
        //        if (! auth()->check()) {
        //            abort(403, __('media-library-extensions::messages.not-authorized'));
        //        }

        $modelType = $request->model_type;
        $modelId = $request->model_id;
        $collectionName = $request->collection_name;
        $model = $this->getModel($modelType, $modelId);

        // TODO check if correct implementation
        //        $this->authorize('uploadMedia', $model);

        if ($request->hasFile('media')) {
            foreach ($request->media as $file) {
                $model
                    ->addMedia($file)
                    ->toMediaCollection($collectionName);
            }
        } else {
            return back()
                ->with('error', __('media-library-extensions::messages.upload-no-files'));
        }

        return back()
            ->with('success', __('media-library-extensions::messages.upload_success'));
    }

    public function destroy(string $mediumId): RedirectResponse
    {
        // TODO check if correct implementation
        //            $this->authorize('deleteMedia', $model);
        //        if (! auth()->check()) {
        //            abort(403, __('media-library-extensions::messages.not-authorized'));
        //        }

        $media = Media::query()->findOrFail($mediumId);

        if ($media) {
            $model = $media->model;

            $media->delete();

            return back()
                ->with('success', __('media-library-extensions::messages.medium-removed'));
        }

        return back()
            ->with('error', __('media-library-extensions::messages.medium-removed'));

    }

    public function setAsFirst(SetAsFirstRequest $request): RedirectResponse
    {
        // TODO authorize
        //        $this->authorize(Permission::DELETE_ALL_MEDIA, $media);
        //        if (! auth()->check()) {
        //            abort(403, __('media-library-extensions::messages.not-authorized'));
        //        }

        $modelType = $request->model_type;
        $modelId = $request->model_id;
        $collectionName = $request->collection_name;
        $model = $this->getModel($modelType, $modelId);
        $mediumId = (int) $request->medium_id;

        $media = $model->getMedia($collectionName);

        // Create an array of media IDs in the desired order
        $orderedIds = $media->pluck('id')->toArray();

        // Remove the media that you want to move to the top from its original position
        $orderedIds = array_filter($orderedIds, fn ($id) => $id !== $mediumId);

        // Add the media you want to move to the top at the start of the array
        array_unshift($orderedIds, $mediumId);

        // Use the setNewOrder method to reorder the media
        Media::setNewOrder($orderedIds);

        return back()
            ->with('success', __('media-library-extensions::messages.medium-set-as-first-in-collection'));
    }
}
