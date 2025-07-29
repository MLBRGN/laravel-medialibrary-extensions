<?php
//
//namespace Mlbrgn\MediaLibraryExtensions\Traits;
//
//use Illuminate\Support\Arr;
//use Illuminate\Database\Eloquent\Model;
//use Spatie\MediaLibrary\MediaCollections\Models\Media;
//
//trait HandlesTemporaryUploads
//{
//    public static function bootHandlesTemporaryUploads(): void
//    {
//        static::created(function (Model $model) {
//            $draftKey = request('draft_key');
//
//            if (!$draftKey) {
//                return;
//            }
//
//            $mediaItems = Media::where('custom_properties->draft_key', $draftKey)
//                ->where('custom_properties->attach_to_model_type', static::class)
//                ->whereNull('model_id')
//                ->get();
//
//            foreach ($mediaItems as $media) {
//                $media->model_id = $model->id;
//                $media->model_type = $model::class;
//                $media->collection_name = Arr::get($media->custom_properties, 'target_collection', 'default');
//                $media->save();
//            }
//        });
//    }
//}
