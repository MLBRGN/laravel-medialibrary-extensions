<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TemporaryUploadPromoter
{
    /**
     * Promote all temporary uploads for the current session
     * and attach them to the given model.
     */
    public function promoteAllForModel(Model $model): void
    {
        $temporaryUploads = TemporaryUpload::where('session_id', session()->getId())->get();

        if ($temporaryUploads->isEmpty()) {
            return;
        }

        $dirty = false;

        foreach ($temporaryUploads as $temporaryUpload) {
            $media = $this->promote($model, $temporaryUpload);

            if (! $media) {
                continue; // skip if promotion failed
            }

            $filename = preg_quote($temporaryUpload->getNameWithExtension(), '#');

            // Match either absolute or relative URL to the temp file
            $pattern = "#(https?:\/\/[^\"'>]+)?/storage/media_temporary/{$filename}#";

            $mediaUrl = $media->getUrl();

            foreach ($model->getHtmlEditorFields() as $field) {
                if (!empty($model->{$field})) {
                    $oldContent = $model->{$field};
                    $newContent = preg_replace($pattern, $mediaUrl, $oldContent);

                    if ($newContent !== $oldContent) {
                        $model->{$field} = $newContent;
                        $dirty = true;
                        Log::info("TemporaryUploadPromoter: replaced temporary URL with permanent media URL for {$filename}");
                    }
                }
            }

            // Remove the temporary file and record
            if (Storage::disk($temporaryUpload->disk)->exists($temporaryUpload->path)) {
                Storage::disk($temporaryUpload->disk)->delete($temporaryUpload->path);
            }

            $temporaryUpload->delete();
        }

        if ($dirty) {
            $model->saveQuietly();
        }
    }


//    public function promoteAllForModel(Model $model): void
//    {
//        $temporaryUploads = TemporaryUpload::where('session_id', session()->getId())->get();
//
//        if ($temporaryUploads->isEmpty()) {
//            return;
//        }
//
//        $dirty = false;
//
//        foreach ($temporaryUploads as $temporaryUpload) {
//            $media = $this->promote($model, $temporaryUpload);
//            $temporaryUploadUrl = $temporaryUpload->getUrl();
//
//            // Replace URLs inside HTML editor fields
//            if ($media && $temporaryUploadUrl) {
//                foreach ($model->getHtmlEditorFields() as $field) {
//                    if (! empty($model->{$field})) {
//                        Log::info("TemporaryUploadPromoter: replace {$temporaryUploadUrl} with {$media->getUrl()}");
//                        $newValue = str_replace($temporaryUploadUrl, $media->getUrl(), $model->{$field});
//                        if ($newValue !== $model->{$field}) {
//                            $model->{$field} = $newValue;
//                            $dirty = true;
//                        }
//                    }
//                }
//            }
//
//            // Remove the temporary file and record
//            if (Storage::disk($temporaryUpload->disk)->exists($temporaryUpload->path)) {
//                Storage::disk($temporaryUpload->disk)->delete($temporaryUpload->path);
//            }
//
//            $temporaryUpload->delete();
//        }
//
//        if ($dirty) {
//            $model->saveQuietly();
//        }
//    }

    /**
     * Promote a single TemporaryUpload record to a Spatie Media record.
     */
    public function promote(Model $model, TemporaryUpload $temporaryUpload): ?Media
    {
        try {
            $customProperties = collect($temporaryUpload->custom_properties)
                ->except(['collections'])
                ->toArray();

            $media = $model
                ->addMediaFromDisk($temporaryUpload->path, $temporaryUpload->disk)
                ->preservingOriginal()
                ->withCustomProperties($customProperties)
                ->usingFileName($temporaryUpload->getNameWithExtension())
                ->toMediaCollection($temporaryUpload->collection_name);

            if ($temporaryUpload->order_column !== null) {
                $media->order_column = $temporaryUpload->order_column;
                $media->save();
            }

            return $media;
        } catch (Exception $e) {
            Log::error(__('media-library-extensions::messages.failed_to_attach_media', [
                'message' => $e->getMessage(),
            ]), [
                'temporary_upload_id' => $temporaryUpload->id,
                'path' => $temporaryUpload->path,
                'disk' => $temporaryUpload->disk,
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return null;
    }
}
