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

    protected ?HasMedia $model;

    protected array $collections;

    protected int $max;

    protected ?string $instanceId;

    protected ?string $clientToken;

    protected ?string $dataSource;

    /**
     * Create a new rule instance.
     *
     * @param HasMediaExtended|string|null $model The model instance or model class name.
     * @param array $collections The media collections to check.
     * @param int $max The maximum number of media items allowed.
     * @param string|null $instanceId The component instance ID (optional).
     * @param string|null $dataSource The data source to use (optional).
     * @param string|null $clientToken The client token (optional).
     */
    public function __construct(
        HasMediaExtended|string|null $model,
        array $collections,
        int $max,
        ?string $instanceId = null,
        ?string $dataSource = 'default',
        ?string $clientToken = null
    ) {
        $this->model = $model instanceof HasMedia ? $model : null;
        $this->collections = $collections;
        $this->max = $max;
        $this->instanceId = $instanceId;
        $this->dataSource = $dataSource;
        $this->clientToken = $clientToken;
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // We do NOT trust the $value if it's a simple count (client-side).
        // If it's an array, it's likely a standard Laravel file upload or similar, so we count it.
        $count = 0;

        // 1. Count permanent media on the model
        if ($this->model) {
            $count += $this->countModelMediaInCollections($this->model, $this->collections, $this->dataSource);
        }

        // 2. Add count from $value if it's an array or a single non-numeric value
        // (to support standard Laravel validation and direct uploads)
        if (is_array($value)) {
            $count += count($value);
        } elseif (filled($value) && ! is_numeric($value)) {
            $count += 1;
        }

        // 3. Count temporary uploads (MLE usage)
        $instanceId = $this->instanceId
            ?? request()->input("mle_instance_map.{$attribute}")
            ?? request()->input('instance_id');

        $count += $this->countTemporaryUploadsInCollections(
            $this->collections,
            $instanceId,
            $this->clientToken,
            $this->dataSource
        );

        if ($count > $this->max) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     */
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
