<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Rules;

use Closure;

class MaxTemporaryUploadCount extends AbstractMediaCountRule
{
    protected int $max;

    public function __construct(array $collections, int $max, ?string $instanceId = null, ?string $dataSource = 'default', ?string $clientToken = null)
    {
        parent::__construct(null, $collections, $instanceId, $dataSource, $clientToken);

        $this->max = $max;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $count = $this->getEffectiveCount($attribute, $value);

        if ($count > $this->max) {
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
