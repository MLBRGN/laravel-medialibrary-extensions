<?php

use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMimeTypes;

//uses()->group('traits');

beforeEach(function () {
    $this->class = new class {
        use InteractsWithOptionsAndConfig, InteractsWithMimeTypes;

        // properties for testing
        public array $options = [];
        public array $config = [];
        public string $modelOrClassName = 'TestModel';
        public array $requiredOptions = ['foo', 'bar'];

        // Public proxies for protected methods
        public function callGetOptions(): array
        {
            return $this->getOptions();
        }

        public function callGetOption(string $key, $default = null)
        {
            return $this->getOption($key, $default);
        }

        public function callHasOption(string $key): bool
        {
            return $this->hasOption($key);
        }

        public function callSetOption(string $key, $value): void
        {
            $this->setOption($key, $value);
        }

        public function callValidateRequiredOptions(): void
        {
            $this->validateRequiredOptions();
        }

        public function callInitializeConfig(array $defaults = []): void
        {
            $this->initializeConfig($defaults);
        }
    };

    // Stub mle_human_mimetype_label for trait
    if (!function_exists('mle_human_mimetype_label')) {
        function mle_human_mimetype_label($mime) {
            return strtoupper($mime);
        }
    }
});

it('gets and sets options correctly', function () {
    $this->class->callSetOption('foo', 'bar');

    expect($this->class->callGetOptions())->toHaveKey('foo', 'bar');
    expect($this->class->callGetOption('foo'))->toBe('bar');
    expect($this->class->callGetOption('nonexistent', 'default'))->toBe('default');
    expect($this->class->callHasOption('foo'))->toBeTrue();
    expect($this->class->callHasOption('nonexistent'))->toBeFalse();
});

it('throws exception when required options are missing', function () {
    $this->class->options = ['foo' => 'value'];
    $this->class->requiredOptions = ['foo', 'bar'];

    $this->class->callValidateRequiredOptions(); // bar is missing, should throw

})->throws(RuntimeException::class, 'Missing required option "bar".');

it('initializes config with defaults and merges options/properties', function () {
    $this->class->options = [
        'frontendTheme' => 'custom-theme',
        'temporaryUploadMode' => true,
    ];

    $this->class->callInitializeConfig([
        'uploadFieldName' => 'customField'
    ]);

    $config = $this->class->config;

    // Defaults + provided overrides + properties + options
    expect($config['uploadFieldName'])->toBe('customField'); // from defaults passed to initializeConfig
    expect($config['frontendTheme'])->toBe('custom-theme'); // from options
    expect($config['temporaryUploadMode'])->toBeTrue(); // from options
    expect($config['modelOrClassName'])->toBe('TestModel'); // from property

    // MIME type fields should exist even if empty
//    expect($config)->toHaveKeys(['allowedMimeTypes', 'allowedMimeTypesHuman']); // TODO
});

it('can get, set, merge, and add config values', function () {
    $this->class->setConfig('key1', 'value1');
    expect($this->class->getConfig('key1'))->toBe('value1');
    expect($this->class->hasConfig('key1'))->toBeTrue();

    $this->class->mergeConfig(['key1' => 'new', 'key2' => 'value2']);
    expect($this->class->getConfig('key1'))->toBe('new');
    expect($this->class->getConfig('key2'))->toBe('value2');

    $this->class->addConfigDefaults(['key2' => 'default2', 'key3' => 'default3']);
    expect($this->class->getConfig('key2'))->toBe('value2'); // unchanged
    expect($this->class->getConfig('key3'))->toBe('default3'); // added default
});

it('handles empty options/config gracefully', function () {
    $this->class->options = [];
    $this->class->config = [];

    expect($this->class->callGetOptions())->toBe([]);
    expect($this->class->getConfig('nonexistent'))->toBeNull();
    expect($this->class->hasConfig('nonexistent'))->toBeFalse();
});
