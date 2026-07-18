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
        $instanceId = $instanceId ?: request()->input('instance_id');

        if (! $clientToken && app()->runningUnitTests()) {
            $clientToken = config('medialibrary-extensions.test_client_token');
        }

        if (! $clientToken) {
            return;
        }

        $requestedDataSource = request()->input('data_source');
        $connectionName = $model->getConnectionName() ?: config('database.default');

        // Try to derive the data source key from the request first; if absent, try to find a
        // data source whose resolved connection matches the model's connection. Fallback to 'default'.
        $dataSource = $requestedDataSource ?: $this->resolveDataSourceFromConnection($connectionName);

        Log::info('TemporaryUploadPromoter: starting promotion scan', [
            'model_class' => get_class($model),
            'model_id' => $model->getKey(),
            'client_token' => $clientToken,
            'instance_id' => $instanceId,
            'requested_data_source' => $requestedDataSource,
            'resolved_db_connection' => $connectionName,
        ]);

        $query = TemporaryUpload::query()
            ->forDataSource($dataSource)
            ->where('client_token', $clientToken);

        if ($instanceId) {
            $query->where('instance_id', $instanceId);
        }

        Log::debug('TemporaryUploadPromoter: query parameters prepared', [
            'data_source' => $dataSource,
            'client_token' => $clientToken,
            'instance_id' => $instanceId,
        ]);

        $temporaryUploads = $query->get();

        Log::info('TemporaryUploadPromoter: temporary uploads fetched', [
            'count' => $temporaryUploads->count(),
        ]);

        // Demo/browser-tests often cannot persist the same client_token from the XHR upload
        // to the final form POST. If nothing matched on token+instance, try a safe fallback:
        // search by instance_id only on the same data source. Guard this to demo/testing.
        if ($temporaryUploads->isEmpty()) {
            $allowFallback = config('medialibrary-extensions.demo_pages_enabled')
                || app()->runningUnitTests()
                || app()->environment('testing')
                || (bool) config('medialibrary-extensions.browser_tests', false);
            if ($allowFallback && $instanceId) {
                Log::warning('TemporaryUploadPromoter: no matches by client_token; retrying by instance_id only (demo/testing fallback)', [
                    'data_source' => $dataSource,
                    'instance_id' => $instanceId,
                ]);

                $fallbackQuery = TemporaryUpload::query()
                    ->forDataSource($dataSource)
                    ->where('instance_id', $instanceId);

                $temporaryUploads = $fallbackQuery->get();

                Log::info('TemporaryUploadPromoter: fallback temporary uploads fetched', [
                    'count' => $temporaryUploads->count(),
                ]);
            }
        }

        if ($temporaryUploads->isEmpty()) {
            return;
        }

        $dirty = false;

        foreach ($temporaryUploads as $temporaryUpload) {
            $disk = $temporaryUpload->disk;
            $pathOriginal = $temporaryUpload->path;
            $pathTrimmed = ltrim($temporaryUpload->path, '/');
            $existsOriginal = Storage::disk($disk)->exists($pathOriginal);
            $existsTrimmed = Storage::disk($disk)->exists($pathTrimmed);

            Log::info('TemporaryUploadPromoter: promoting single temporary upload', [
                'temporary_upload_id' => $temporaryUpload->id,
                'data_source' => $dataSource,
                'disk' => $disk,
                'disk_root' => config("filesystems.disks.{$disk}.root"),
                'path' => $pathOriginal,
                'path_trimmed' => $pathTrimmed,
                'exists_original' => $existsOriginal,
                'exists_trimmed' => $existsTrimmed,
                'collection' => $temporaryUpload->collection_name,
                'file_name' => $temporaryUpload->file_name,
                'order_column' => $temporaryUpload->order_column,
            ]);

            $media = $this->promote($model, $temporaryUpload);

            if (! $media) {
                Log::warning("TemporaryUploadPromoter - promotion failed for temporary upload {$temporaryUpload->id}");

                continue;
            }

            $temporaryDisk = $temporaryUpload->disk;
            $temporaryDiskUrl = rtrim(Storage::disk($temporaryDisk)->url(''), '/');

            $fieldChanges = 0;
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
                    $fieldChanges++;
                } else {
                    Log::warning("TemporaryUploadPromoter - no replacements made in field '{$field}' for {$temporaryUpload->file_name}");
                }
            }

            Log::info('TemporaryUploadPromoter: promotion completed for temporary upload', [
                'temporary_upload_id' => $temporaryUpload->id,
                'media_id' => optional($media)->id,
                'media_url' => $media->getUrl(),
                'html_fields_changed' => $fieldChanges,
            ]);

            // Cleanup temp file + DB record
            $existed = Storage::disk($temporaryDisk)->exists($temporaryUpload->path);
            if ($existed) {
                Storage::disk($temporaryDisk)->delete($temporaryUpload->path);
            }

            Log::debug('TemporaryUploadPromoter: cleaned up temporary storage and record', [
                'temporary_upload_id' => $temporaryUpload->id,
                'file_existed' => $existed,
                'path' => $temporaryUpload->path,
                'disk' => $temporaryDisk,
            ]);

            $temporaryUpload->delete();
        }

        if ($dirty) {
            $model->saveQuietly();
            Log::info('TemporaryUploadPromoter: model saved after promotions', [
                'model_id' => $model->id,
            ]);
        } else {
            Log::warning('TemporaryUploadPromoter - no changes detected, model not saved', ['model_id' => $model->id]);
        }
    }

    protected function resolveDataSourceFromConnection(string $connection): string
    {
        // Fast path: if default connection matches, use 'default'
        if ($connection === config('database.default')) {
            return 'default';
        }

        // Inspect configured data sources and return the first whose resolved connection matches
        $configured = (array) config('medialibrary-extensions.data_sources', []);
        $resolver = app(DataSourceResolver::class);

        foreach ($configured as $key => $cfg) {
            try {
                $resolved = $resolver->resolveConnection($key);
                if ($resolved === $connection) {
                    return (string) $key;
                }
            } catch (\Throwable $e) {
                // ignore invalid entries
            }
        }

        // Fallback
        return 'default';
    }

    protected function promote(Model $model, TemporaryUpload $temporaryUpload): ?Media
    {
        //        dump($temporaryUpload->disk);
        //        dump($temporaryUpload->path);
        //        dump(config("filesystems.disks.{$temporaryUpload->disk}"));
        //        dump(Storage::disk($temporaryUpload->disk)->exists($temporaryUpload->path));
        //        dump(storage_path($temporaryUpload->disk . $temporaryUpload->path));
        //        dump(Storage::disk($temporaryUpload->disk)->allFiles());
        //        dump(file_exists(storage_path($temporaryUpload->disk . $temporaryUpload->path)));
        //        try {

        //        dump(app('filesystem')->disk($temporaryUpload->disk));
        //        dump([
        //            'disk' => $temporaryUpload->disk,
        //            'path' => $temporaryUpload->path,
        //            'path_trimmed' => ltrim($temporaryUpload->path, '/'),
        //            'disk_root' => config("filesystems.disks.{$temporaryUpload->disk}.root"),
        //            'resolved_path' => Storage::disk($temporaryUpload->disk)->path(ltrim($temporaryUpload->path, '/')),
        //            'exists_original' => Storage::disk($temporaryUpload->disk)->exists($temporaryUpload->path),
        //            'exists_trimmed' => Storage::disk($temporaryUpload->disk)->exists(ltrim($temporaryUpload->path, '/')),
        //            'files' => Storage::disk($temporaryUpload->disk)->allFiles(),
        //        ]);
        Log::debug('TemporaryUploadPromoter: attaching media from disk', [
            'temporary_upload_id' => $temporaryUpload->id,
            'disk' => $temporaryUpload->disk,
            'path' => $temporaryUpload->path,
            'file_name' => $temporaryUpload->file_name,
            'collection' => $temporaryUpload->collection_name,
        ]);

        try {
            // Extra diagnostics to trace file resolution issues in browser tests
            try {
                $disk = $temporaryUpload->disk;
                $path = (string) $temporaryUpload->path;
                $pathTrimmed = ltrim($path, '/');

                $existsOriginal = \Storage::disk($disk)->exists($path);
                $existsTrimmed = \Storage::disk($disk)->exists($pathTrimmed);
                $absolutePath = null;
                try {
                    $absolutePath = \Storage::disk($disk)->path($pathTrimmed);
                } catch (\Throwable $e) {
                    // some drivers may not support path(); ignore
                }

                Log::info('TemporaryUploadPromoter: pre-attach existence check', [
                    'temporary_upload_id' => $temporaryUpload->id,
                    'disk' => $disk,
                    'path' => $path,
                    'path_trimmed' => $pathTrimmed,
                    'exists_original' => $existsOriginal,
                    'exists_trimmed' => $existsTrimmed,
                    'absolute_path' => $absolutePath,
                ]);
            } catch (\Throwable $e) {
                // ignore existence logging failures
            }

            $media = $model
                ->addMediaFromDisk($temporaryUpload->path, $temporaryUpload->disk)
                ->preservingOriginal()
                ->usingFileName($temporaryUpload->file_name)
                ->toMediaCollection($temporaryUpload->collection_name);

            if ($temporaryUpload->order_column !== null) {
                $media->order_column = $temporaryUpload->order_column;
                $media->save();
            }

            Log::info('TemporaryUploadPromoter: media attached to model', [
                'model_class' => get_class($model),
                'model_id' => $model->getKey(),
                'media_id' => $media->id,
                'collection' => $temporaryUpload->collection_name,
            ]);

            return $media;
        } catch (Exception $e) {
            Log::error('TemporaryUploadPromoter: failed to attach media', [
                'temporary_upload_id' => $temporaryUpload->id,
                'disk' => $temporaryUpload->disk,
                'path' => $temporaryUpload->path,
                'file_name' => $temporaryUpload->file_name,
                'collection' => $temporaryUpload->collection_name,
                'error' => $e->getMessage(),
            ]);

            throw $e; // rethrow to preserve behavior
        }
        //        } catch (Exception $e) {
        //            Log::error('TemporaryUploadPromoter - failed to attach media', [
        //                'temporary_upload_id' => $temporaryUpload->id,
        //                'path' => $temporaryUpload->path,
        //                'disk' => $temporaryUpload->disk,
        //                'error' => $e->getMessage(),
        //            ]);

        //            return null;
        //        }
    }

    protected function replaceTemporaryUrlsInHtml(
        string $html,
        string $temporaryDiskUrl,
        string $mediaUrl,
        string $filename
    ): string {
        $filenamePattern = preg_quote($filename, '#');

        // Be robust: consume any optional scheme+host (including protocol-relative)
        // before the temporary path so we don't leave the original host in place and
        // end up with duplicated hosts after replacement.
        // Examples matched:
        //  - /storage/media_temporary/.../file.png
        //  - http://127.0.0.1:8000/storage/media_temporary/.../file.png
        //  - //localhost/storage/media_temporary/.../file.png
        $hostPattern = '(?:(?:https?:)?\/\/[^"\'"<>\s]+)?';
        $tempBasePattern = '\/storage\/media_temporary\/[^"\')>\s]*?';
        $pattern = '#'.$hostPattern.$tempBasePattern.$filenamePattern.'#iu';

        $newHtml = preg_replace($pattern, $mediaUrl, $html);

        if ($newHtml !== null && $newHtml !== $html) {
            Log::debug('TemporaryUploadPromoter: temporary URL(s) replaced in HTML', [
                'pattern' => $pattern,
                'replacement' => $mediaUrl,
            ]);

            // Final guard: if any accidental duplicated scheme+host remains (e.g.,
            // http://hosthttp://host/...), collapse it by keeping the rightmost occurrence.
            // This is defensive and should normally be a no-op thanks to the host-consuming regex above.
            $newHtml = preg_replace('#(https?:\/\/[^\s"\'"<>]+)(https?:\/\/)#iu', '$1', $newHtml);
        }

        return $newHtml ?? $html;
    }
}
