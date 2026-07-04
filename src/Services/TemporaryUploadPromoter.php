<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TemporaryUploadPromoter
{
    public function promoteAllForModel(Model $model, ?string $instanceId = null, ?string $clientToken = null): void
    {
        $clientToken = $clientToken ?: (request()->input('client_token') ?: request()->cookie('mle_client_token'));

        if (! $clientToken && app()->runningUnitTests()) {
            $clientToken = config('medialibrary-extensions.test_client_token');
        }

        if (! $clientToken) {
            return;
        }

        $query = TemporaryUpload::where('client_token', $clientToken);

        if ($instanceId) {
            $query->where('instance_id', $instanceId);
        }

        $temporaryUploads = $query->get();

        if ($temporaryUploads->isEmpty()) {
            return;
        }

        $dirty = false;

        foreach ($temporaryUploads as $temporaryUpload) {
            $media = $this->promote($model, $temporaryUpload);

            if (! $media) {
                Log::warning("TemporaryUploadPromoter - promotion failed for temporary upload {$temporaryUpload->id}");

                continue;
            }

            $temporaryDisk = $temporaryUpload->disk;
            $temporaryDiskUrl = rtrim(Storage::disk($temporaryDisk)->url(''), '/');

            foreach ($model->getHtmlEditorFields() as $field) {
                $html = $model->{$field};

                if (! is_string($html) || trim($html) === '') {
                    continue;
                }

                $newHtml = $this->replaceTemporaryUrlsInHtml(
                    $html,
                    $temporaryDiskUrl,
                    $media->getUrl(),
                    $temporaryUpload->file_name
                );

                if ($newHtml !== $html) {
                    $model->{$field} = $newHtml;
                    $dirty = true;
                } else {
                    Log::warning("TemporaryUploadPromoter - no replacements made in field '{$field}' for {$temporaryUpload->file_name}");
                }
            }

            // Cleanup temp file + DB record
            if (Storage::disk($temporaryDisk)->exists($temporaryUpload->path)) {
                Storage::disk($temporaryDisk)->delete($temporaryUpload->path);
            }

            $temporaryUpload->delete();
        }

        if ($dirty) {
            $model->saveQuietly();
        } else {
            Log::warning('TemporaryUploadPromoter - no changes detected, model not saved', ['model_id' => $model->id]);
        }
    }

    protected function promote(Model $model, TemporaryUpload $temporaryUpload): ?Media
    {
        try {
            $media = $model
                ->addMediaFromDisk($temporaryUpload->path, $temporaryUpload->disk)
                ->preservingOriginal()
                ->usingFileName($temporaryUpload->file_name)
                ->toMediaCollection($temporaryUpload->collection_name);

            if ($temporaryUpload->order_column !== null) {
                $media->order_column = $temporaryUpload->order_column;
                $media->save();
            }

            return $media;
        } catch (Exception $e) {
            Log::error('TemporaryUploadPromoter - failed to attach media', [
                'temporary_upload_id' => $temporaryUpload->id,
                'path' => $temporaryUpload->path,
                'disk' => $temporaryUpload->disk,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    protected function replaceTemporaryUrlsInHtml(
        string $html,
        string $temporaryDiskUrl,
        string $mediaUrl,
        string $filename
    ): string {
        $filenamePattern = preg_quote($filename, '#');

        // Match relative or absolute URLs pointing to media_temporary
        $pattern = '#(?:'.preg_quote($temporaryDiskUrl, '#').'|)(/storage/media_temporary/.*?)'.$filenamePattern.'#iu';

        $newHtml = preg_replace($pattern, $mediaUrl, $html);

        if ($newHtml !== $html) {
            Log::warning('TemporaryUploadPromoter - temporary URL replaced in HTML', [
                'old_url_pattern' => $pattern,
                'new_url' => $mediaUrl,
            ]);
        }

        return $newHtml;
    }
}
