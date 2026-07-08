<?php

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Exceptions\UploadException;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\UploadPreparerService;
use Mlbrgn\MediaLibraryExtensions\Support\MediaUploadContext;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;

class StoreSingleTemporaryAction
{
    // TODO use MediaService::countTemporaryUploadsInCollections() or countMediaInCollections()
    use ChecksMediaLimits;

    public function __construct(
        protected MediaService $mediaService,
        protected UploadPreparerService $uploadPreparerService,
    ) {}

    public function execute(
        StoreSingleRequest $request
    ): RedirectResponse|JsonResponse {

        $dataSource = $request->input('data_source');

        // Strict: only accept base_id; derive instance ID server-side
        $baseId = (string) $request->input('base_id');
        $instanceId = InstanceManager::getInstanceId($baseId);

        $clientToken = $request->input('client_token')
            ?? $request->cookie('mle_client_token')
            ?? (string) Str::ulid();

        try {

            $prepared = $this->uploadPreparerService
                ->prepareSingleUpload($request);

        } catch (UploadException $e) {

            return MediaResponse::error(
                $request,
                $baseId,
                $e->getMessage()
            );
        }

        if ($this->temporaryUploadsHaveAnyMedia(
            $prepared->collections,
            $instanceId,
            $clientToken,
            $dataSource
        )) {
            return MediaResponse::error(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.only_one_medium_allowed')
            );
        }

        $disk = config('medialibrary-extensions.media_disks.temporary');

        $directory = '';

        $safeFilename = Str::slug(
            pathinfo(
                $prepared->originalName,
                PATHINFO_FILENAME
            ),
            '-'
        ).'.'.$prepared->file->getClientOriginalExtension();

        $path = Storage::disk($disk)->putFileAs(
            $directory,
            $prepared->file,
            $safeFilename
        );

        $userId = Auth::check()
            ? Auth::id()
            : null;

        $temporaryUpload = $this->mediaService->make(TemporaryUpload::class, $dataSource);

        $temporaryUpload->fill([
            'disk' => $disk,
            'path' => $path,
            'name' => $safeFilename,
            'file_name' => $safeFilename,
            'collection_name' => $prepared->collectionName,
            'mime_type' => $prepared->mimeType,
            'size' => $prepared->size,
            'user_id' => $userId,
            'client_token' => $clientToken,
            //            'instance_id' => $instanceId ?: null,
            'instance_id' => $instanceId,
            'order_column' => 0,
            'custom_properties' => [
                'collections' => $prepared->collections,
                'priority' => 0,
            ],
        ]);
        $temporaryUpload->save();

        app(MediaUploadContext::class)->set(
            $temporaryUpload->instance_id,
            $temporaryUpload->client_token
        );

        return MediaResponse::success(
            $request,
            $baseId,
            __('medialibrary-extensions::messages.upload_success'),
            [
                'saved_file' => $safeFilename,
                'client_token' => $clientToken,
            ]
        );
    }
}
