<?php

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\UploadPreparerService;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

class StoreSingleTemporaryAction
{
    use ChecksMediaLimits;

    public function __construct(
        protected MediaService $mediaService,
        protected UploadPreparerService $uploadPreparerService,
    ) {}

    public function execute(
        StoreSingleRequest $request
    ): RedirectResponse|JsonResponse {

        $dataSource = $request->input('data_source');

        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id;
        $instanceId = $request->input('instance_id');

        Log::info('StoreSingleTemporaryUpload - dataSource '.$dataSource);
        try {

            $prepared = $this->uploadPreparerService
                ->prepareSingleUpload($request);

        } catch (UploadException $e) {

            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                $e->getMessage()
            );
        }

        if ($this->temporaryUploadsHaveAnyMedia(
            $prepared->collections,
            $instanceId,
            null,
            $dataSource
        )) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
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

        Storage::disk($disk)->putFileAs(
            $directory,
            $prepared->file,
            $safeFilename
        );

        Log::info(
            'StoreSingleTemporaryUpload - stored file: '
            .$safeFilename.' in directory '.$directory
        );

        $sessionId = $request->session()->getId();

        Log::info('StoreSingleTemporaryUpload: session id: '.$sessionId);

        $userId = Auth::check()
            ? Auth::id()
            : null;

        $temporaryUpload = $this->mediaService->make(TemporaryUpload::class, $dataSource);

        $temporaryUpload->fill([
            'disk' => $disk,
            'path' => "{$directory}/{$safeFilename}",
            'name' => $safeFilename,
            'file_name' => $safeFilename,
            'collection_name' => $prepared->collectionName,
            'mime_type' => $prepared->mimeType,
            'size' => $prepared->size,
            'user_id' => $userId,
            'session_id' => $sessionId,
            'instance_id' => $instanceId ?: null,
            'order_column' => 0,
            'custom_properties' => [
                'collections' => $prepared->collections,
                'priority' => 0,
            ],
        ]);
        $temporaryUpload->save();

        Log::info(
            'StoreSingleTemporaryUpload - stored db record in db '.$temporaryUpload->getConnectionName()
        );

        return MediaResponse::success(
            $request,
            $initiatorId,
            $mediaManagerId,
            __('medialibrary-extensions::messages.upload_success'),
            ['saved_file' => $safeFilename]
        );
    }
}
