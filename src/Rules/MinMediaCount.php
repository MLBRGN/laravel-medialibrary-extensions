<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Rules;

use Closure;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;

class MinMediaCount extends AbstractMediaCountRule
{
    protected int $min;

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
        parent::__construct($model, $collections, $instanceId, $dataSource, $clientToken);

        $this->min = $min;
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
        $count = $this->getEffectiveCount($attribute, $value);

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
