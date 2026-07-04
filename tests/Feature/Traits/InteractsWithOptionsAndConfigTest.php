<?php

use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMimeTypes;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;

// uses()->group('traits');

beforeEach(function () {
    $this->class = new class
    {
        use InteractsWithMimeTypes, InteractsWithOptionsAndConfig;

        public string $modelOrClassName = 'TestModel';

        public array $requiredOptions = ['foo', 'bar'];

        public ?string $mediaUploadRoute = null;

        public ?string $youtubeUploadRoute = null;

        public ?string $mediaManagerPreviewUpdateRoute = null;

        public ?string $mediumSetAsFirstRoute = null;

        public ?string $mediaDestroyRoute = null;

        public ?string $mediumRestoreRoute = null;

        public ?string $mediaLabPreviewBaseUpdateRoute = null;

        public ?string $mediaLabPreviewOriginalUpdateRoute = null;

        public function __construct()
        {
            $this->options = [];
            $this->config = [];
        }

        public function callValidateRequiredOptions(): void
        {
            $this->validateRequiredOptions();
        }

        public function callInitializeConfig(array $defaults = []): void
        {
            $this->resolveConfig($defaults);
        }
    };

    // Stub mle_human_mimetype_label for trait
    if (! function_exists('mle_human_mimetype_label')) {
        function mle_human_mimetype_label($mime)
        {
            return strtoupper($mime);
        }
    }
});

it('gets and sets options correctly', function () {
    $this->class->setOption('foo', 'bar');

    expect($this->class->getOption('foo'))->toBe('bar');
    expect($this->class->getOption('nonexistent', 'default'))->toBe('default');
    expect($this->class->hasOption('foo'))->toBeTrue();
    expect($this->class->hasOption('nonexistent'))->toBeFalse();

    expect($this->class->getOptions())->toBe([
        'foo' => 'bar',
    ]);
});

it('throws exception when required options are missing', function () {
    $this->class->setOption('foo', 'value');
    $this->class->requiredOptions = ['foo', 'bar'];

    $this->class->callValidateRequiredOptions();
})->throws(RuntimeException::class, 'Missing required option "bar".');

it('initializes config with defaults and merges options/properties', function () {
    $this->class->setOption('theme', 'custom-theme');
    $this->class->setOption('temporaryUploadMode', true);

    $this->class->callInitializeConfig([
        'uploadFieldName' => 'customField',
        'modelOrClassName' => 'TestModel',
    ]);

    // defaults override check
    expect($this->class->getConfig('uploadFieldName'))->toBe('customField');

    // options override check
    expect($this->class->getConfig('theme'))->toBe('custom-theme');
    expect($this->class->getConfig('temporaryUploadMode'))->toBeTrue();

    // property merge check
    expect($this->class->getConfig('modelOrClassName'))->toBe('TestModel');
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
    expect($this->class->getOptions())->toBe([]);

    expect($this->class->getConfig('nonexistent'))->toBeNull();
    expect($this->class->hasConfig('nonexistent'))->toBeFalse();
});

it('resolves config routes', function () {
    $this->class->mediaUploadRoute = 'upload-route';
    $this->class->youtubeUploadRoute = 'youtube-route';

    $this->class->callInitializeConfig();

    $routes = $this->class->getConfig('routes');
    // We expect the config keys defined in configRouteKeys
    expect($routes)->toHaveKey('mediaUpload', 'upload-route');
    expect($routes)->toHaveKey('youtubeUpload', 'youtube-route');
});

it('merges config recursively', function () {
    $this->class->setConfig('nested', ['a' => 1]);
    $this->class->mergeConfig(['nested' => ['b' => 2]]);

    expect($this->class->getConfig('nested'))->toBe(['a' => 1, 'b' => 2]);
});
