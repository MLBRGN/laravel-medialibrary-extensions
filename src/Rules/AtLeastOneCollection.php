<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AtLeastOneCollection implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // TODO replace with other way
        $collections = request()->only([
            'image_collection', // TODO
            'document_collection', // TODO
            'video_collection', // TODO
            'audio_collection', // TODO
            'youtube_collection', // TODO
        ]);

        if (collect($collections)->filter()->isEmpty()) {
            $fail('medialibrary-extensions::messages.at_least_one_collection_is_required')->translate();
        }
    }
}
