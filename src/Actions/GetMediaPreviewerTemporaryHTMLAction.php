<?php

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Support\Facades\Blade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerPreview;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest;

class GetMediaPreviewerTemporaryHTMLAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(GetMediaPreviewerHTMLRequest $request): JsonResponse|Response
    {
        $initiatorId = $request->input('initiator_id');
//        $disk = config('media-library-extensions.temporary_upload_disk');
//        $basePath = config('media-library-extensions.temporary_upload_path');
//        $temporaryUploadsUuid = $request->input('temporary_uploads_uuid');
//        $directory = "{$basePath}/{$temporaryUploadsUuid}";


        $component = new MediaManagerPreview(
            id: $initiatorId,
            model: null,
            imageCollection: $request->input('image_collection'),
            documentCollection: $request->input('document_collection'),
            youtubeCollection: $request->input('youtube_collection'),
            frontendTheme: $request->input('frontend_theme'),
            destroyEnabled: $request->input('destroy_enabled'),
            setAsFirstEnabled: $request->input('set_as_first_enabled'),
            showMediaUrl: $request->input('show_media_url'),
            showOrder: $request->input('show_order'),
            temporaryUploads: true,
        );

        $html = Blade::renderComponent($component);

        return response()->json([
            'html' => $html,
            'success' => true,
            'target' => $initiatorId,
        ]);
//        if (empty($temporaryUploadsUuid) || !Storage::disk($disk)->exists($directory)) {
//            return response()->json([
//                'html' => '<div>No temporary files found.</div>',
//                'success' => true,
//                'target' => $initiatorId,
//            ]);
//        }

//        $files = collect(Storage::disk($disk)->files($directory))
//            ->map(fn ($path) => [
//                'url' => Storage::disk($disk)->url($path),
//                'name' => basename($path),
//                'extension' => pathinfo($path, PATHINFO_EXTENSION),
//            ])
//            ->groupBy(function ($file) {
//                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
//                $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt'];
//
//                if (in_array(strtolower($file['extension']), $imageExtensions)) {
//                    return 'images';
//                } elseif (in_array(strtolower($file['extension']), $documentExtensions)) {
//                    return 'documents';
//                }
//
//                return 'others';
//            });
//        $files = collect(Storage::disk($disk)->files($directory))
//            ->map(fn ($path) => [
//                'url' => Storage::disk($disk)->url($path),
//                'name' => basename($path),
//                'extension' => pathinfo($path, PATHINFO_EXTENSION),
//            ])
//            ->all();  // <-- flatten collection to array, no grouping
//        $component = new MediaManagerPreview(
//            id: $initiatorId,
//            model: null,
//            imageCollection: $request->input('image_collection'),
//            documentCollection: $request->input('document_collection'),
//            youtubeCollection: $request->input('youtube_collection'),
//            frontendTheme: $request->input('frontend_theme'),
//            destroyEnabled: $request->input('destroy_enabled'),
//            setAsFirstEnabled: $request->input('set_as_first_enabled'),
//            showMediaUrl: $request->input('show_media_url'),
//            showOrder: $request->input('show_order'),
//            temporaryUploads: true,
//        );
//
//        $html = Blade::renderComponent($component);
//
//        return response()->json([
//            'html' => $html,
//            'success' => true,
//            'target' => $initiatorId,
//        ]);
    }
}
