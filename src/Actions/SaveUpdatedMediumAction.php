<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SaveUpdatedMediumRequest;

class SaveUpdatedMediumAction
{
    public function execute(SaveUpdatedMediumRequest $request): JsonResponse|RedirectResponse
    {
        $initiatorId = $request->initiator_id;

        Log::info('All request data', $request->all());

        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        $mediumId = $request->input('medium_id');
        $collection = $request->input('collection');
        $file = $request->file('file');

        abort_unless(class_exists($modelType), 400, 'Invalid model type');

        $model = $modelType::findOrFail($modelId);

        // Find the existing media to replace
        $existingMedia = $model
            ->getMedia($collection)
            ->firstWhere('id', $mediumId);

        $name = null;
        $customProperties = [];

        if ($existingMedia) {
            $name = $existingMedia->name;
            $customProperties = $existingMedia->custom_properties ?? [];

            $existingMedia->delete();
        }

        $fileAdder = $model->addMedia($file);

        if ($name) {
            $fileAdder->usingName($name);
        }

        $fileAdder->withCustomProperties($customProperties)
            ->toMediaCollection($collection);

        // Return a response; replace 'blaat' with real initiator or relevant data
        return MediaResponse::success($request, $initiatorId, __('media-library-extensions::messages.medium_removed'));
    }
}
