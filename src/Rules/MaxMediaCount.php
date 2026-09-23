<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Rules;

use Closure;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;

class MaxMediaCount extends AbstractMediaCountRule
{
    protected int $max;

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
        parent::__construct($model, $collections, $instanceId, $dataSource, $clientToken);

        $this->max = $max;
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
