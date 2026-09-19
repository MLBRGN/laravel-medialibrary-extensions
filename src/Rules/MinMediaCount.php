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

        // Resolve context
        $clientToken = $this->clientToken ?: app(\Mlbrgn\MediaLibraryExtensions\Support\ClientContext::class)->resolve();
        $dataSource = $this->dataSource ?: 'default';
        if ($dataSource === 'default' && request()->has('data_source')) {
            $dataSource = request()->input('data_source');
        }

        // 1. Count permanent media on the model
        if ($this->model) {
            $count += $this->countModelMediaInCollections($this->model, $this->collections, $dataSource);
        }

        // 2. Count temporary uploads
        // Prioritize mle_instance_ids array from component registrations
        $instanceId = $this->instanceId ?: array_unique(array_filter(array_merge(
            (array) request()->input('mle_instance_ids', []),
            (array) request()->input('instance_id', [])
        )));

        $count += $this->countTemporaryUploadsInCollections(
            $this->collections,
            $instanceId,
            $clientToken,
            $dataSource
        );

        // 3. Fallback: check _mle_cnt_{id} if count is still 0
        // (This aligns validation with what the user sees in the component)
        if ($count === 0) {
            $lookFor = (array) $instanceId;

            if (! empty($lookFor)) {
                foreach ($lookFor as $id) {
                    if (request()->has('_mle_cnt_' . $id)) {
                        $count += (int) request()->input('_mle_cnt_' . $id);
                    }
                }
            }

            // Final fallback: check the attribute itself if it's a numeric string
            if ($count === 0 && is_string($value) && is_numeric($value)) {
                $count = (int) $value;
            }
        }

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
