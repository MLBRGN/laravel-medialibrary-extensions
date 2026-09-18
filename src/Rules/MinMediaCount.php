<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;
use Spatie\MediaLibrary\HasMedia;

class MinMediaCount implements ValidationRule
{
    use ChecksMediaLimits;

    protected ?HasMedia $model;

    protected array $collections;

    protected int $min;

    protected ?string $instanceId;

    protected ?string $clientToken;

    protected ?string $dataSource;
    
    protected bool $multiple;

    /**
     * Create a new rule instance.
     *
     * @param HasMediaExtended|string|null $model The model instance or model class name.
     * @param array $collections The media collections to check.
     * @param int $min The minimum number of media items required.
     * @param string|null $instanceId The component instance ID (optional).
     * @param string|null $dataSource The data source to use (optional).
     * @param string|null $clientToken The client token (optional).
     * @param bool $multiple Whether this is for a multiple media manager (optional, defaults to true).
     */
    public function __construct(
        HasMediaExtended|string|null $model,
        array $collections,
        int $min,
        ?string $instanceId = null,
        ?string $dataSource = 'default',
        ?string $clientToken = null,
        bool $multiple = true
    ) {
        $this->model = $model instanceof HasMedia ? $model : null;
        $this->collections = $collections;
        $this->min = $min;
        $this->instanceId = $instanceId;
        $this->dataSource = $dataSource;
        $this->clientToken = $clientToken;
        $this->multiple = $multiple;
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
        // We do NOT trust the $value (which is the client-side count).
        // Instead, we count the actual media in the database and temporary storage.
        $count = 0;

        // 1. Count permanent media on the model
        if ($this->model) {
            $count += $this->countModelMediaInCollections($this->model, $this->collections, $this->dataSource);
        }

        // 2. Count temporary uploads
        $instanceId = $this->instanceId ?? request()->input('instance_id');

        $count += $this->countTemporaryUploadsInCollections(
            $this->collections,
            $instanceId,
            $this->clientToken,
            $this->dataSource
        );

        if ($count < $this->min) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        if ($this->min === 1) {
            return $this->multiple
                ? __('medialibrary-extensions::messages.at_least_one_medium_required')
                : __('medialibrary-extensions::messages.one_medium_required');
        }

        return __('medialibrary-extensions::messages.this_collection_requires_at_least_:items_items', [
            'items' => $this->min,
        ]);
    }
}
