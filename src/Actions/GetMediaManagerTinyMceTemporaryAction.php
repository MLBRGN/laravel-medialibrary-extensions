<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerTinyMceRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManager;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerTinymce;

class GetMediaManagerTinyMceTemporaryAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}


    public function execute(GetMediaManagerTinyMceRequest $request): View
    {
        $initiatorId = $request->input('initiator_id');
        $model = $this->mediaService->resolveModel(
            $request->input('model_type'),
            $request->input('model_id'),
        );

        return view('media-library-extensions::media-manager-tinymce', compact('initiatorId', 'model'));

        // Instantiate the component
//        $component = new MediaManagerTinymce(
//            modelOrClassName: $model,
//            imageCollection: $request->input('image_collection', ''),
//            documentCollection: $request->input('document_collection', ''),
//            youtubeCollection: $request->input('youtube_collection', ''),
//            videoCollection: $request->input('video_collection', ''),
//            audioCollection: $request->input('audio_collection', ''),
//            id: $initiatorId,
//            frontendTheme: $request->input('frontend_theme', null),
//        );
//
//        // Return the component as a view
//        return $component->render();
    }

    /**
     * @throws Exception
     */
//    public function execute(GetMediaManagerTinyMceRequest $request): JsonResponse|Response
//    {
//        $initiatorId = $request->input('initiator_id');
//        $model = $this->mediaService->resolveModel(
//            $request->input('model_type'),
//            $request->input('model_id'),
//        );
//
//        $imageCollection = $request->input('image_collection', '');
//        $documentCollection = $request->input('document_collection', '');
//        $youtubeCollection = $request->input('youtube_collection', '');
//        $videoCollection = $request->input('video_collection', '');
//        $audioCollection = $request->input('audio_collection', '');
//
//        $collections = collect([
//            $imageCollection,
//            $documentCollection,
//            $youtubeCollection,
//            $videoCollection,
//            $audioCollection,
//        ])
//            ->filter(fn ($collection) => !empty($collection)) // removes null, '', false
//            ->all();
//
//        $totalMediaCount = 0;
//
//        foreach ($collections as $collectionName) {
//            $totalMediaCount += $model->getMedia($collectionName)->count();
//        }
//
//        $component = new MediaManager(
//            modelOrClassName: $model,
//            id: $initiatorId,
//            imageCollection: $imageCollection,
//            documentCollection: $documentCollection,
//            youtubeCollection: $youtubeCollection,
//            videoCollection: $videoCollection,
//            audioCollection: $audioCollection,
//            frontendTheme: $request->input('frontend_theme'),
//            destroyEnabled: $request->input('destroy_enabled') === 'true',
//            setAsFirstEnabled: $request->input('set_as_first_enabled') === 'true',
//            showOrder: $request->input('show_order') === 'true',
//            showMenu: $request->input('show_menu') === 'true',
////            temporaryUploads: $request->input('temporary_uploads') === 'true',
//        );
//
//        $html = Blade::renderComponent($component);
//
//        return response($html, 200)
//            ->header('Content-Type', 'text/html');
////        return response()->json([
////            'html' => $html,
////            'mediaCount' => $totalMediaCount,
////            'success' => true,
////            'target' => $initiatorId,
////        ]);
//    }
}
