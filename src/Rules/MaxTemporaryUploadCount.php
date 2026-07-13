<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class MaxTemporaryUploadCount implements ValidationRule
{
    use ChecksMediaLimits;

    protected array $collections;

    protected int $max;

    protected ?string $instanceId;

    protected ?string $clientToken;

    protected ?string $dataSource;

    public function __construct(array $collections, int $max, ?string $instanceId = null, ?string $dataSource = 'default', ?string $clientToken = null)
    {
        $this->collections = $collections;
        $this->max = $max;
        $this->instanceId = $instanceId;
        $this->dataSource = $dataSource;
        $this->clientToken = $clientToken;
    }

    public function validate(string $attribute, $value, Closure $fail): void
    {
        $newCount = is_array($value) ? count($value) : 1;

        $existingCount = $this->countTemporaryUploadsInCollections(
            $this->collections,
            $this->instanceId,
            $this->clientToken,
            $this->dataSource,
        );

        Log::debug('mle.validation.max_temp_upload_count', [
            'collections' => $this->collections,
            'new_count' => $newCount,
            'existing_count' => $existingCount,
            'max' => $this->max,
            'instanceId' => $this->instanceId,
            'dataSource' => $this->dataSource,
            'clientToken' => $this->clientToken ? substr($this->clientToken, 0, 4).'…'.substr($this->clientToken, -4) : null,
        ]);

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
