<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class YouTubeUrl implements ValidationRule
{
    /**
     * Invoke the validation rule.
     *
     * @param  \Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^https?:\/\/(www\.)?(youtube\.com|youtu\.be)\//', $value)) {
            $fail(__('media-library-extensions::messages.invalid_youtube_url'));
        }
    }
}
