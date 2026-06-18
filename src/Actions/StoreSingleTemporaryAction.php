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
        $clientToken = $request->input('client_token')
            ?? $request->cookie('mle_client_token')
            ?? (string) Str::ulid();

//        Log::info("StoreSingleTemporaryAction: dataSource = $dataSource");
//        Log::info("StoreSingleTemporaryAction: instanceId = $instanceId");
//        Log::info("StoreSingleTemporaryAction: instanceId = $clientToken");
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

        $userId = Auth::check()
            ? Auth::id()
            : null;

        $temporaryUpload = $this->mediaService->make(TemporaryUpload::class, $dataSource);

//        Log::info('StoreSingleTemporaryAction - Connection name: '.$temporaryUpload->getConnectionName());
//        Log::info(
//            'StoreSingleTemporaryAction - Database connection: '.$temporaryUpload->getConnection()->getName()
//        );

        $temporaryUpload->fill([
            'disk' => $disk,
            'path' => "{$directory}/{$safeFilename}",
            'name' => $safeFilename,
            'file_name' => $safeFilename,
            'collection_name' => $prepared->collectionName,
            'mime_type' => $prepared->mimeType,
            'size' => $prepared->size,
            'user_id' => $userId,
            'client_token' => $clientToken,
            'instance_id' => $instanceId ?: null,
            'order_column' => 0,
            'custom_properties' => [
                'collections' => $prepared->collections,
                'priority' => 0,
            ],
        ]);
//        Log::info('StoreSingleTemporaryAction - Default DB: '.config('database.default'));

//        Log::info(
//            'StoreSingleTemporaryAction - TemporaryUpload connection: '.
//            ($temporaryUpload->getConnectionName() ?? 'null')
//        );

//        Log::info(
//            'StoreSingleTemporaryAction - Resolved connection: '.
//            $temporaryUpload->getConnection()->getName()
//        );
//        Log::info('StoreSingleTemporaryAction - ' . json_encode(config('database.connections')));
        $temporaryUpload->save();

//        Log::info(
//            'StoreSingleTemporaryUpload - stored db record in db '.$temporaryUpload->getConnectionName()
//        );

        return MediaResponse::success(
            $request,
            $initiatorId,
            $mediaManagerId,
            __('medialibrary-extensions::messages.upload_success'),
            ['saved_file' => $safeFilename]
        );
    }
}
