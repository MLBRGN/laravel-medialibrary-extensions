<?php

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Unified trait for managing configuration and options in Blade components.
 *
 * Design principle:
 * - Blade views only read from $config via $this->getConfig().
 * - Components can define public/protected properties and options.
 * - After construction, all relevant state (properties + options) is merged into $config.
 *
 * NOTE: options are optional (duh!)
 * Blade views should only interact with config or with constructor properties, never with properties defined outside
 * of the constructor or with options directly
 */
trait InteractsWithOptionsAndConfig
{
    // used to map properties to config array, only keys in this array
    // are added to the config array
    protected array $configKeys = [
        'modelOrClassName',
//        'medium',
        'singleMedium',
        'collections',
        'multiple',
        'disabled',
        'readonly',
        'selectable',
        'frontendTheme',
        'uploadFieldName',
        'temporaryUploadMode',
        'csrfToken',
        'model',
        'modelType',
        'modelId',
        'options',
        'id',

        'mediaUploadRoute',
        'mediaManagerPreviewUpdateRoute',
        'youtubeUploadRoute',
        'mediumSetAsFirstRoute',
        'mediumDestroyRoute',
        'mediumRestoreRoute',
        'mediaManagerLabPreviewUpdateRoute'

        // any other properties you want in config
    ];

    /* -----------------------------------------------------------------
     |  OPTION MANAGEMENT
     | -----------------------------------------------------------------
     */

    protected function getOptions(): array
    {
        return property_exists($this, 'options') ? $this->options : [];
    }

    protected function getOption(string $key, mixed $default = null): mixed
    {
        $options = $this->getOptions();

        if (array_key_exists($key, $options)) {
            return $options[$key];
        }

        //        Log::debug(sprintf('[%s] Option "%s" not set, using default.', static::class, $key));
        return $default;
    }

    protected function hasOption(string $key): bool
    {
        $options = $this->getOptions();

        return array_key_exists($key, $options) && $options[$key] !== null;
    }

    /**
     * Set or update a single option.
     */
    protected function setOption(string $key, mixed $value): void
    {
        if (! property_exists($this, 'options') || ! is_array($this->options)) {
            $this->options = [];
        }

        $this->options[$key] = $value;
    }

    protected function validateRequiredOptions(): void
    {
        if (! property_exists($this, 'requiredOptions')) {
            return;
        }

        $options = $this->getOptions();
        foreach ($this->requiredOptions as $key) {
            if (! array_key_exists($key, $options) || $options[$key] === null) {
                $message = sprintf('[%s] Missing required option "%s".', static::class, $key);
                Log::error($message);
                throw new RuntimeException($message);
            }
        }
    }

    /* -----------------------------------------------------------------
     |  CONFIG MANAGEMENT
     | -----------------------------------------------------------------
     */

    public function getConfig(string $key, mixed $default = null): mixed
    {
        if (! property_exists($this, 'config') || ! is_array($this->config)) {
            return $default;
        }

        return Arr::get($this->config, $key, $default);
    }

    public function hasConfig(string $key): bool
    {
        if (! property_exists($this, 'config') || ! is_array($this->config)) {
            return false;
        }

        return Arr::has($this->config, $key) && ! is_null(Arr::get($this->config, $key));
    }

    public function setConfig(string $key, mixed $value): void
    {
        if (! property_exists($this, 'config') || ! is_array($this->config)) {
            $this->config = [];
        }

        Arr::set($this->config, $key, $value);
    }

    public function mergeConfig(array $values): void
    {
        if (! property_exists($this, 'config') || ! is_array($this->config)) {
            $this->config = [];
        }

        $this->config = array_replace_recursive($this->config, $values);
    }

    public function addConfigDefaults(array $defaults): void
    {
        if (! property_exists($this, 'config') || ! is_array($this->config)) {
            $this->config = [];
        }

        foreach ($defaults as $key => $value) {
            if (! Arr::has($this->config, $key)) {
                Arr::set($this->config, $key, $value);
            }
        }
    }

    /**
     * Merge all options and all explicitly defined public/protected properties
     * into $config, optionally with default values.
     *
     * Order of precedence:
     *   defaults < existing config < properties < non-null options
     */
    protected function initializeConfig(array $defaults = []): void
    {
        // Hardcoded default values
        $defaultOptionValues = [
            'showDestroyButton' => true,
            'showMediaEditButton' => true,
            'showMenu' => true,
            'showOrder' => false,
            'showSetAsFirstButton' => true,
            'showUploadForm' => true,
            'showYouTubeUploadForm' => true,
            'showUploadForms' => true,
            'temporaryUploadMode' => false,
            'uploadFieldName' => 'medium',
            'frontendTheme' => config('media-library-extensions.frontend_theme', 'bootstrap-5'),
            'useXhr' => config('media-library-extensions.use_xhr', true),
            'csrfToken' => csrf_token(),
//            'selectable' => false,
//            'disabled' => false,
//            'readonly' => false,
//            'multiple' => false,
            // allowedMimeTypes handled by separate trait
            // allowedMimeTypesHuman is produced
        ];

        // Merge provided defaults **over** hardcoded defaults
        $config = array_replace_recursive($defaultOptionValues, $defaults);

        if (! isset($this->configKeys)) {
            throw new RuntimeException(sprintf('The config keys must be set in %s', static::class));
        }
        // Include explicitly listed properties
        foreach ($this->configKeys ?? [] as $key) {
            if (property_exists($this, $key)) {
                $config[$key] = $this->{$key};
            }
        }

        // Merge non-null options
        if (property_exists($this, 'options') && is_array($this->options)) {
            $filteredOptions = array_filter($this->options, fn ($v) => ! is_null($v));
            $config = array_replace_recursive($config, $filteredOptions);
        }

        // Automatically sync MIME type fields
        if (in_array(InteractsWithMimeTypes::class, class_uses_recursive(static::class))) {
            $this->syncAllowedMimeTypes($config);
        }

        $this->config = $config;
    }
}
