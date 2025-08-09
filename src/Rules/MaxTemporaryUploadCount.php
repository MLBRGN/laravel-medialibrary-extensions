<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;

class MaxTemporaryUploadCount implements ValidationRule
{
    protected string $modelClassName;

    protected string $collectionName;

    protected int $max;

    public function __construct(
        string $modelClassName,
        string $collectionName,
        int $max)
    {
        $this->modelClassName = $modelClassName;
        $this->collectionName = $collectionName;
        $this->max = $max;
    }

    public function validate(string $attribute, $value, Closure $fail): void
    {
        $allMedia = TemporaryUpload::forCurrentSession($this->collectionName);
        $newCount = is_array($value) ? count($value) : 1;
        $existingCount = $allMedia->count();

        if (($existingCount + $newCount) > $this->max) {
            $fail($this->message());
        }
    }

    public function message(): string
    {
        return __('media-library-extensions::messages.this_collection_can_contain_up_to_:items_items', ['items' => $this->max]);
    }
}
