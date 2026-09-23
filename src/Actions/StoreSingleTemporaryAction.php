<?php

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Exceptions\UploadException;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaModelResolver;
use Mlbrgn\MediaLibraryExtensions\Services\UploadPreparerService;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Support\MediaUploadContext;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class StoreSingleTemporaryAction
{
    use ChecksMediaLimits;

    public function __construct(
        protected MediaModelResolver $mediaModelResolver,
        protected UploadPreparerService $uploadPreparerService,
    ) {}

    public function execute(
        StoreSingleRequest $request
    ): RedirectResponse|JsonResponse {

        $dataSource = $request->input('data_source', 'default');

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

        if ($this->getEffectiveMediaCount(
            collections: $prepared->collections,
            instanceId: $instanceId,
            dataSource: $dataSource,
            ignoreClientToken: true
        ) > 0) {
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

        // Store file with a unique name to avoid collisions.
        $path = Storage::disk($disk)->putFile(
            $directory,
            $prepared->file
        );

        $userId = Auth::check()
            ? Auth::id()
            : null;

        $temporaryUpload = $this->mediaModelResolver->instantiateTemporaryUpload($dataSource);

        $modelType = (string) $request->input('model_type');
        try {
            $modelType = $this->mediaModelResolver->resolveModelClass($modelType);
            $modelType = (new $modelType)->getMorphClass();
        } catch (\Throwable) {
            // fallback to input if resolution fails
        }

        $customProperties = [
            'collections' => $prepared->collections,
            'priority' => 0,
            'model_type' => $modelType,
        ];

        if (str_contains($prepared->mimeType, 'image')) {
            $dimensions = getimagesize($prepared->file->getPathname());
            if ($dimensions) {
                $customProperties['width'] = $dimensions[0];
                $customProperties['height'] = $dimensions[1];
            }
        }

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
            'custom_properties' => $customProperties,
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
