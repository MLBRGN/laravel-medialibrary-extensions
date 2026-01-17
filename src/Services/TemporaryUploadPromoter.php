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
    public function promoteAllForModel(Model $model): void
    {
        $temporaryUploads = TemporaryUpload::where('session_id', session()->getId())->get();

//        dump(print_r($temporaryUploads, true));
        if ($temporaryUploads->isEmpty()) {
            Log::info('TemporaryUploadPromoter: no temporary uploads found for this session');
            dump('TemporaryUploadPromoter: no temporary uploads found');
            return;
        }

        Log::info('TemporaryUploadPromoter: found temporary uploads', ['count' => $temporaryUploads->count()]);
        dump('TemporaryUploadPromoter: found temporary uploads');
        $dirty = false;

        foreach ($temporaryUploads as $temporaryUpload) {
            Log::info("TemporaryUploadPromoter: promoting temporary upload", [
                'id' => $temporaryUpload->id,
                'file_name' => $temporaryUpload->file_name,
                'path' => $temporaryUpload->path,
                'disk' => $temporaryUpload->disk,
            ]);
            dump('TemporaryUploadPromoter: promoting temporary upload');

            $media = $this->promote($model, $temporaryUpload);

            if (! $media) {
                Log::warning("TemporaryUploadPromoter: promotion failed for temporary upload {$temporaryUpload->id}");
                dump('TemporaryUploadPromoter: promotion failed for temporary upload');
                continue;
            }

            Log::info("TemporaryUploadPromoter: promotion successful", [
                'media_id' => $media->id,
                'media_url' => $media->getUrl(),
            ]);
            dump('TemporaryUploadPromoter: promotion successful');

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
                    Log::info("TemporaryUploadPromoter: updated HTML field '{$field}' with new media URL", [
                        'temporary_file' => $temporaryUpload->file_name,
                        'new_media_url' => $media->getUrl(),
                    ]);
                    dump('TemporaryUploadPromoter: updated HTML field');
                } else {
                    Log::info("TemporaryUploadPromoter: no replacements made in field '{$field}' for {$temporaryUpload->file_name}");
                    dump('TemporaryUploadPromoter: no replacements made in field');
                }
            }

            // Cleanup temp file + DB record
            if (Storage::disk($temporaryDisk)->exists($temporaryUpload->path)) {
                Storage::disk($temporaryDisk)->delete($temporaryUpload->path);
                Log::info("TemporaryUploadPromoter: deleted temporary file", ['path' => $temporaryUpload->path]);
            }

            $temporaryUpload->delete();
            Log::info("TemporaryUploadPromoter: deleted temporary upload record", ['id' => $temporaryUpload->id]);
        }

        if ($dirty) {
            $model->saveQuietly();
            Log::info("TemporaryUploadPromoter: model saved with updated HTML fields", ['model_id' => $model->id]);
        } else {
            Log::info("TemporaryUploadPromoter: no changes detected, model not saved", ['model_id' => $model->id]);
        }
    }

    protected function promote(Model $model, TemporaryUpload $temporaryUpload): ?Media
    {
        dump('TemporaryUploadPromoter promote: ' . $temporaryUpload->file_name);
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
            Log::error('TemporaryUploadPromoter: failed to attach media', [
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
        $pattern = '#(?:' . preg_quote($temporaryDiskUrl, '#') . '|)(/storage/media_temporary/.*?)' . $filenamePattern . '#iu';

        $newHtml = preg_replace($pattern, $mediaUrl, $html);

        if ($newHtml !== $html) {
            Log::info('TemporaryUploadPromoter: temporary URL replaced in HTML', [
                'old_url_pattern' => $pattern,
                'new_url' => $mediaUrl,
            ]);
        }

        return $newHtml;
    }
}
