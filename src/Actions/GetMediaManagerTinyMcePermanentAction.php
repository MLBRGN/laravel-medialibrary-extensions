<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerTinyMceRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerTinymce;
use Spatie\MediaLibrary\HasMedia;

class GetMediaManagerTinyMcePermanentAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}


    public function execute(GetMediaManagerTinyMceRequest $request): View
    {
        $initiatorId = 'something_for_now';
        $model = $this->mediaService->resolveModel(
            $request->input('model_type'),
            $request->input('model_id'),
        );

        $frontendTheme = $request->input('frontend_theme') ? $request->input('frontend_theme')  : config('medialibrary-extensions.frontend_theme', 'bootstrap-5');
        $modelOrClassName = $request->input('model_or_class_name');
        $model = null;
        $modelType = null;
        $modelId = null;
        $temporaryUpload = null;
        $multiple = false;
        $setAsFirstEnabled = true;

        if ($modelOrClassName instanceof HasMedia) {
            $model = $modelOrClassName;
            $modelType = $modelOrClassName->getMorphClass();
            $modelId = $modelOrClassName->getKey();
        } elseif (is_string($modelOrClassName)) {
            if (! class_exists($modelOrClassName)) {
                throw new \InvalidArgumentException(__('media-library-extensions::messages.class_does_not_exist', ['class_name' => $modelOrClassName]));
            }

            if (! is_subclass_of($modelOrClassName, HasMedia::class)) {
                throw new \InvalidArgumentException(__('media-library-extensions::messages.class_must_implement', ['class_name' => HasMedia::class]));
            }

            $model = null;
            $modelType = $modelOrClassName;
            $modelId = null;
            $temporaryUpload = true;
        }

        // Override: Always disable "set-as-first" when multiple files disabled
//        if (!$multiple) {
//            $setAsFirstEnabled = false;
//        }
//
//        // Override: Always set upload enabled to false when no document collections provided
//        if (!$this->imageCollection && !$this->documentCollection && !$this->videoCollection && !$this->audioCollection) {
//            $this->uploadEnabled = false;
//        }
//
//        if (!$this->imageCollection && !$this->documentCollection && !$this->videoCollection && !$this->audioCollection && !$this->youtubeCollection) {
//            throw new Exception(__('media-library-extensions::messages.no_media_collections'));
//        }
//
//        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');
//
//        // the routes, "set-as-first" and "destroy" are "medium specific" routes, so not defined here
//        $this->previewUpdateRoute = route(mle_prefix_route('preview-update'));
//        $this->youtubeUploadRoute = route(mle_prefix_route('media-upload-youtube'));
//
//        if ($this->multiple) {
//            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-multiple'));
//            $this->uploadFieldName = config('media-library-extensions.upload_field_name_multiple');
////            $this->id = $this->id.'-media-manager-multiple';
//            $this->id = $this->id.'-mmm';
//        } else {
//            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-single'));
//            $this->uploadFieldName = config('media-library-extensions.upload_field_name_single');
////            $this->id = $this->id.'-media-manager-single';
//            $this->id = $this->id.'-mms';
//        }
//
//        // Config array passed to view
//        $this->config = [
//            'id' => $this->id,
//            'model_type' => $this->modelType,
//            'model_id' => $this->modelId,
//            'image_collection' => $this->imageCollection,
//            'document_collection' => $this->documentCollection,
//            'video_collection' => $this->videoCollection,
//            'audio_collection' => $this->audioCollection,
//            'youtube_collection' => $this->youtubeCollection,
//            'media_upload_route' => $this->mediaUploadRoute,
//            'preview_update_route' => $this->previewUpdateRoute,
//            'youtube_upload_route' => $this->youtubeUploadRoute,
//            'csrf_token' => csrf_token(),
//            'frontend_theme' => $this->frontendTheme,
//            'destroy_enabled' => $this->destroyEnabled,
//            'set_as_first_enabled' => $this->setAsFirstEnabled,
//            'show_order' => $this->showOrder,
//            'show_menu' => $this->showMenu,
//            'temporary_upload' => $this->temporaryUpload ? 'true' : 'false',
//            'multiple' => $this->multiple,
//            'use_xhr' => $this->useXhr,
//        ];

        return view('media-library-extensions::media-manager-tinymce', compact('initiatorId', 'model', 'temporaryUpload', 'id'));

//        return view('media-manager-wrapper', [
//            'modelType' => get_class($model),
//            'id' => $request->input('initiator_id', 'media-manager'),
//            'imageCollection' => $request->input('image_collection', ''),
//            'documentCollection' => $request->input('document_collection', ''),
//            'youtubeCollection' => $request->input('youtube_collection', ''),
//            'videoCollection' => $request->input('video_collection', ''),
//            'audioCollection' => $request->input('audio_collection', ''),
//        ]);
//        // Instantiate the component
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
