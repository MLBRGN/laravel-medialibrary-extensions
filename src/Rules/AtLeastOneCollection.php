<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AtLeastOneCollection implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $collections = request()->only([
            'image_collection',
            'document_collection',
            'video_collection',
            'audio_collection',
            'youtube_collection',
        ]);

        if (collect($collections)->filter()->isEmpty()) {
            $fail('At least one collection is required.');
        }
    }
}
