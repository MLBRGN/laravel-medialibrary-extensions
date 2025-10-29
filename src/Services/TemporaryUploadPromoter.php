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

            // replace URLs inside HTML editor fields
            if ($media && $tempUrl = $temporaryUpload->getUrl()) {
                if (
                    property_exists($model, 'htmlEditorFields') &&
                    is_iterable($model->htmlEditorFields)
                    ) {
                    foreach ($model->htmlEditorFields as $field) {
                        if (!empty($model->{$field})) {
                            $newValue = str_replace($tempUrl, $media->getUrl(), $model->{$field});
                            if ($newValue !== $model->{$field}) {
                                $model->{$field} = $newValue;
                                $dirty = true;
                            }
                        }
                    }
                }
            }

            // remove the temporary file and record
            if (Storage::disk($temporaryUpload->disk)->exists($temporaryUpload->path)) {
                Storage::disk($temporaryUpload->disk)->delete($temporaryUpload->path);
            }

            $temporaryUpload->delete();
        }

        if ($dirty) {
            $model->saveQuietly();
        }
    }

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
