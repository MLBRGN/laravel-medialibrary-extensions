<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;
use Spatie\MediaLibrary\HasMedia;

class MaxMediaCount implements ValidationRule
{
    use ChecksMediaLimits;

    protected HasMedia $model;

    protected array $collections;

    protected int $max;

    protected ?string $dataSource;

    public function __construct(HasMediaExtended $model, array $collections, int $max, ?string $dataSource = 'default')
    {
        $this->model = $model;
        $this->collections = $collections;
        $this->max = $max;
        $this->dataSource = $dataSource;
    }

    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void
    {
        $newCount = is_array($value) ? count($value) : 1;

        $existingCount = $this->countModelMediaInCollections($this->model, $this->collections, $this->dataSource);

        if (($existingCount + $newCount) > $this->max) {
            $fail($this->message());
        }
    }

    public function message(): string
    {
        if ($this->max === 1) {
            return __('medialibrary-extensions::messages.only_one_medium_allowed');
        }

        return __('medialibrary-extensions::messages.this_collection_can_contain_up_to_:items_items', [
            'items' => $this->max,
        ]);
    }
}
