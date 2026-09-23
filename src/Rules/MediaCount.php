<?php

namespace Mlbrgn\MediaLibraryExtensions\Rules;

use Closure;

class MediaCount extends AbstractMediaCountRule
{
    protected ?int $min = null;

    protected ?int $max = null;

    protected ?int $exactly = null;

    /**
     * Set the minimum number of media items.
     */
    public function min(int $min): static
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Set the maximum number of media items.
     */
    public function max(int $max): static
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Set the exact number of media items.
     */
    public function exactly(int $exactly): static
    {
        $this->exactly = $exactly;

        return $this;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $count = $this->getEffectiveCount($attribute, $value);

        if ($this->exactly !== null && $count !== $this->exactly) {
            $fail($this->getExactlyMessage());

            return;
        }

        if ($this->min !== null && $count < $this->min) {
            $fail($this->getMinMessage());
        }

        if ($this->max !== null && $count > $this->max) {
            $fail($this->getMaxMessage());
        }
    }

    /**
     * Get the message for minimum count violation.
     */
    protected function getMinMessage(): string
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

    /**
     * Get the message for maximum count violation.
     */
    protected function getMaxMessage(): string
    {
        if ($this->max === 1) {
            return __('medialibrary-extensions::messages.only_one_medium_allowed');
        }

        return __('medialibrary-extensions::messages.this_collection_can_contain_up_to_:items_items', [
            'items' => $this->max,
        ]);
    }

    /**
     * Get the message for exact count violation.
     */
    protected function getExactlyMessage(): string
    {
        return __('medialibrary-extensions::messages.this_collection_must_contain_exactly_:items_items', [
            'items' => $this->exactly,
        ]);
    }
}
