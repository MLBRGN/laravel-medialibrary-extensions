<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class MaxTemporaryUploadCount implements ValidationRule
{
    use ChecksMediaLimits;

    protected array $collections;

    protected int $max;

    protected ?string $instanceId;

    public function __construct(array $collections, int $max, ?string $instanceId = null)
    {
        $this->collections = $collections;
        $this->max = $max;
        $this->instanceId = $instanceId;
    }

    public function validate(string $attribute, $value, Closure $fail): void
    {
        $newCount = is_array($value) ? count($value) : 1;

        $existingCount = $this->countTemporaryUploadsInCollections($this->collections, $this->instanceId);

        if (($existingCount + $newCount) > $this->max) {
            $fail($this->message());
        }
    }

    public function message(): string
    {
        if ($this->max === 1) {
            return __('media-library-extensions::messages.only_one_medium_allowed');
        }

        return __('media-library-extensions::messages.this_collection_can_contain_up_to_:items_items', [
            'items' => $this->max,
        ]);
    }
}
