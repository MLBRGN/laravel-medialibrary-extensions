<?php

namespace Mlbrgn\MediaLibraryExtensions\Traits;

trait InteractsWithOptions
{
    /**
     * Applies an options array to the component’s public properties,
     * respecting existing default values.
     */
    protected function mapOptionsToProperties(array $options): void
    {
        // Fetch current public properties (including defaults)
        $defaults = get_object_vars($this);

        // Merge defaults and options (options override defaults)
        $merged = array_merge($defaults, $options);

        // Apply merged values to existing properties
        foreach ($merged as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}
