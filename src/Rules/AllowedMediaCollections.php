<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;

class AllowedMediaCollections implements ValidationRule
{
    public function __construct(
        protected HasMediaExtended $model,
    ) {}

    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        $requested = array_values(array_unique(array_filter((array) $value)));
        $allowed = array_values(array_unique(array_filter(
            $this->model->allowedMediaCollections()
        )));

        if ($allowed === []) {
            return;
        }

        if (! empty(array_diff($requested, $allowed))) {
            $fail(__('medialibrary-extensions::messages.selected_media_collection_not_allowed'));
        }
    }
}

//class AllowedMediaCollections implements ValidationRule
//{
//    public function __construct(
//        protected ?HasMediaExtended $model,
//        protected ?string $modelClass,
//        protected bool $temporaryUpload,
////        protected array $requestedCollections,
//    ) {}
//
//
//
//    public function validate(
//        string $attribute,
//        mixed $value,
//        Closure $fail
//    ): void {
//        $requestedCollections = is_array($value) ? $value : [];
//
//        $allowedCollections = $this->allowedCollections();
//
//        if ($allowedCollections === []) {
//            return;
//        }
//
//        if (array_diff($requestedCollections, $allowedCollections)) {
//            $fail(__('medialibrary-extensions::messages.selected_media_collection_not_allowed'));
//        }
//    }
////    public function validate(
////        string $attribute,
////        mixed $value,
////        Closure $fail
////    ): void {
////        $allowedCollections = $this->allowedCollections();
////
////        if ($allowedCollections === []) {
////            return;
////        }
////
////        if (array_diff($this->requestedCollections, $allowedCollections)) {
////            $fail(__('medialibrary-extensions::messages.selected_media_collection_not_allowed'));
////        }
////    }
//
//    protected function allowedCollections(): array
//    {
//        if ($this->model) {
//            return $this->model->allowedMediaCollections();
//        }
//
//        if ($this->temporaryUpload && $this->modelClass) {
//            /** @var HasMediaExtended $model */
//            $model = new $this->modelClass;
//
//            return $model->allowedMediaCollections();
//        }
//
//        return [];
//    }
//}

