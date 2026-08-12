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
            $fail('medialibrary-extensions::messages.selected_media_collection_not_allowed')->translate();
        }
    }
}

