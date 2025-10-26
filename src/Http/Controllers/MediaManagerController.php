<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Actions\DeleteMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\DeleteTemporaryUploadAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaManagerTinyMceAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetMediumAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetTemporaryUploadAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultipleMediaAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSingleMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreUpdatedMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyTemporaryMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerTinyMceRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetMediumAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryMediumAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\UpdateMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerController extends Controller
{
    public function store(
        StoreSingleRequest $request,
        StoreSingleMediumAction $storeSingleMediumAction
    ): RedirectResponse|JsonResponse {
        return $storeSingleMediumAction->execute($request);
    }

    public function storeMany(
        StoreMultipleRequest $request,
        StoreMultipleMediaAction $storeMultipleMediaAction
    ): RedirectResponse|JsonResponse {
        return $storeMultipleMediaAction->execute($request);
    }

    public function storeYouTube(
        StoreYouTubeVideoRequest $request,
        StoreYouTubeVideoAction $storeYouTubeMediaAction
    ): RedirectResponse|JsonResponse {
        return $storeYouTubeMediaAction->execute($request);
    }

    public function destroy(
        DestroyRequest $request,
        Media $media,
        DeleteMediumAction $deleteMediumAction
    ): RedirectResponse|JsonResponse {
        return $deleteMediumAction->execute($request, $media);
    }

    public function temporaryUploadDestroy(
        DestroyTemporaryMediumRequest $request,
        TemporaryUpload $temporaryUpload,
        DeleteTemporaryUploadAction $deleteTemporaryUploadAction
    ): RedirectResponse|JsonResponse {
        return $deleteTemporaryUploadAction->execute($request, $temporaryUpload);
    }

    public function setAsFirst(
        SetMediumAsFirstRequest $request,
        SetMediumAsFirstAction $setMediumAsFirstAction
    ): RedirectResponse|JsonResponse {
        return $setMediumAsFirstAction->execute($request);
    }

    public function setTemporaryUploadAsFirst(
        SetTemporaryMediumAsFirstRequest $request,
        SetTemporaryUploadAsFirstAction $setTemporaryUploadAsFirstAction
    ): RedirectResponse|JsonResponse {
        return $setTemporaryUploadAsFirstAction->execute($request);
    }

    public function saveUpdatedMedium(
        UpdateMediumRequest $request,
        StoreUpdatedMediumAction $saveUpdatedMediumAction
    ): RedirectResponse|JsonResponse {
        return $saveUpdatedMediumAction->execute($request);
    }

    public function saveUpdatedTemporaryUpload(
        UpdateMediumRequest $request,
        StoreUpdatedMediumAction $saveUpdatedMediumAction
    ): RedirectResponse|JsonResponse {
        return $saveUpdatedMediumAction->execute($request);
    }

    public function getUpdatedPreviewerHTML(
        GetMediaPreviewerHTMLRequest $request,
        GetMediaPreviewerHTMLAction $getMediaPreviewerHTMLAction
    ): RedirectResponse|JsonResponse {
        return $getMediaPreviewerHTMLAction->execute($request);
    }

    //    public function tinyMce(GetMediaManagerTinyMceRequest $request, GetMediaManagerTinyMceAction $getMediaManagerTinyMceAction): View
    //    {
    //        return $getMediaManagerTinyMceAction->execute($request);
    //    }

    public function restoreOriginal(Media $medium)
    {
        $originalPath = "{$medium->id}/{$medium->file_name}";

        if (!Storage::disk('originals')->exists($originalPath)) {
            return back()->with('error', 'Original file not found.');
        }

        try {
            // Overwrite current media file with original
            $content = Storage::disk('originals')->get($originalPath);
            file_put_contents($medium->getPath(), $content);

            // Optionally regenerate conversions if needed
            // $medium->generateConversions();

            Log::info("Restored original for media [{$medium->id}].");
            return back()->with('success', 'Original restored successfully.');
        } catch (\Throwable $e) {
            Log::error("Failed to restore original for media [{$medium->id}]: {$e->getMessage()}");
            return back()->with('error', 'Failed to restore original.');
        }
    }

//    public function restoreOriginal(Media $medium)
//    {
//        $originalPath = $medium->id . '/' . $medium->file_name;
//
//        if (!Storage::disk('originals')->exists($originalPath)) {
//            return back()->with('error', 'Original file not found.');
//        }
//
//        // Get the original content
//        $content = Storage::disk('originals')->get($originalPath);
//
//        // Overwrite the media's file
//        $mediaPath = $medium->getPath(); // path to current media file
//        Storage::put($mediaPath, $content);
//
//        // Regenerate conversions
////        $medium->generateConversions();
//
//        return back()->with('success', 'Original restored successfully.');
//    }

    public function tinyMce(GetMediaManagerTinyMceRequest $request, GetMediaManagerTinyMceAction $getMediaManagerTinyMceAction): View
    {
        return $getMediaManagerTinyMceAction->execute($request);
//        dd($request);
//        // Get the first existing model or create it if none exists
//        $modelType = $request->input('model_type');
//        $modelId = $request->input('model_id');
//        $id = 'something_for_now'; // TODO
//        $multiple = false;
//        $collections = json_decode(request()->string('collections'), true);
//        $options = json_decode(request()->string('options'), true);
//
//        $model = null;
//        if ($modelType && $modelId) {
//            $model = $modelType::findOrFail($modelId);
//        }
//        $modelOrClassName = $model ?? $modelType;
//
////        dd([
////            'id' => $id,
////            'modelOrClassName' => $modelOrClassName,
////            'multiple' => $multiple,
////            'collections' => $collections,
////            'options' => $options,
////        ]);
//        return view('media-library-extensions::media-manager-tinymce-wrapper', [
//            'id' => $id,
//            'modelOrClassName' => $modelOrClassName,
//            'multiple' => $multiple,
//            'collections' => $collections,
//            'options' => $options,
//        ]);
    }
}
