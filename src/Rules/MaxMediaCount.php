<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Spatie\MediaLibrary\HasMedia;

class MaxMediaCount implements ValidationRule
{
    protected HasMedia $model;

    protected string $collectionName;

    protected int $max;

    public function __construct(
        HasMedia $model,
        string $collectionName,
        int $max)
    {
        $this->model = $model;
        $this->collectionName = $collectionName;
        $this->max = $max;
    }

    public function validate(string $attribute, $value, Closure $fail): void
    {
        $newCount = is_array($value) ? count($value) : 1;
        $existingCount = $this->model->getMedia($this->collectionName)->count();

        if (($existingCount + $newCount) > $this->max) {
            $fail($this->message());
        }
    }

    public function message(): string
    {
        return __('media-library-extensions::messages.you-can-only-have-:items-items-in-this-collection', ['items' => $this->max]);
    }
}
