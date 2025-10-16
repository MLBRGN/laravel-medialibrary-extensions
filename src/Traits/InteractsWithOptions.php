<?php

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Illuminate\Support\Facades\Log;
use RuntimeException;

trait InteractsWithOptions
{
//    protected array $options = [];

    /**
     * Safely get an option by key.
     *
     * @param  string  $key
     * @param  mixed|null  $default
     * @param  bool  $throwWhenMissing  Whether to throw/log when missing.
     * @return mixed
     */
    public function getOption(string $key, mixed $default = null, bool $throwWhenMissing = true): mixed
    {
        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }

        if ($throwWhenMissing) {
            $message = sprintf(
                '[%s] Missing required option "%s" in %s',
                static::class,
                $key,
                debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? 'unknown context'
            );

            // log (for non-fatal warning)
            Log::warning($message);

            // throw (for strict runtime errors)
            throw new RuntimeException($message);

        }

        return $default;
    }

    /**
     * Shortcut to check if an option key exists and has a non-null value.
     */
    protected function hasOption(string $key): bool
    {
        return array_key_exists($key, $this->options) && $this->options[$key] !== null;
    }

    /**
     * Applies an options array to the component’s public properties.
     */
    protected function mapOptionsToProperties(array $options): void
    {
//        $this->options = $options; // keep raw options accessible
        $defaults = get_object_vars($this);
        $merged = array_merge($defaults, $options);

        foreach ($merged as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}

//namespace Mlbrgn\MediaLibraryExtensions\Traits;
//
//trait InteractsWithOptions
//{
//    /**
//     * Applies an options array to the component’s public properties,
//     * respecting existing default values.
//     */
//    protected function mapOptionsToProperties(array $options): void
//    {
//        // Fetch current public properties (including defaults)
//        $defaults = get_object_vars($this);
//
//        // Merge defaults and options (options override defaults)
//        $merged = array_merge($defaults, $options);
//
//        // Apply merged values to existing properties
//        foreach ($merged as $key => $value) {
//            if (property_exists($this, $key)) {
//                $this->{$key} = $value;
//            }
//        }
//    }
//}
