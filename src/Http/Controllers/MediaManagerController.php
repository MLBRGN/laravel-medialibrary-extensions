<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Http\Controllers;

/** @noinspection PhpUnused */

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\SpatieMediaLibraryExtensions\Http\Request\MediaManagerUploadSingleRequest;
use Mlbrgn\SpatieMediaLibraryExtensions\Http\Requests\MediaManagerDestroyRequest;
use Mlbrgn\SpatieMediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;
use Mlbrgn\SpatieMediaLibraryExtensions\Http\Requests\SetMediumAsFirstInCollectionRequest;
use Mlbrgn\SpatieMediaLibraryExtensions\Models\Media;

class MediaManagerController extends Controller
{
    protected function getModel(string $modelType, string $modelId): ?Model
    {

        abort_if(! class_exists($modelType), 400, 'Invalid model type');

        $model = (new $modelType)::find($modelId); // Retrieve the model instance by ID

        abort_if(! $model, 400, 'Model not found');

        return $model;
    }

    public function mediaUploadSingle(MediaManagerUploadSingleRequest $request): RedirectResponse
    {

        $modelType = $request->model_type;
        $modelId = $request->model_id;
        $collectionName = $request->collection_name;
        $model = $this->getModel($modelType, $modelId);
        $this->authorize('uploadMedia', $model);
        $medium = $request->medium;

        if ($request->hasFile('medium')) {
            $model
                ->addMedia($medium)
                ->toMediaCollection($collectionName);
        } else {
            return back()
                ->with('error', __('app.upload-no-files'));
        }

        return back()
            ->with('success', __('app.upload-successful'));
    }

    public function mediaUploadMultiple(MediaManagerUploadMultipleRequest $request): RedirectResponse
    {

        $modelType = $request->model_type;
        $modelId = $request->model_id;
        $collectionName = $request->collection_name;
        $model = $this->getModel($modelType, $modelId);

        if ($request->hasFile('media')) {
            foreach ($request->media ?? [] as $file) {
                $model
                    ->addMedia($file)
                    ->toMediaCollection($collectionName);
            }
        } else {
            return back()
                ->with('error', __('app.upload-no-files'));
        }

        return back()
            ->with('success', __('app.upload-successful'));
    }

    public function setMediumAsFirstInCollection(SetMediumAsFirstInCollectionRequest $request): RedirectResponse
    {

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
            ->with('success', __('app.medium-set-as-first-in-collection'));
    }

    public function mediaDestroy(\Spatie\MediaLibrary\MediaCollections\Models\Media $media, MediaManagerDestroyRequest $request): RedirectResponse
    {
        //        $this->authorize(Permission::DELETE_ALL_MEDIA, $media);

        $media->destroy($media->id);

        return back()
            ->with('success', __('app.medium-removed'));
    }
}
