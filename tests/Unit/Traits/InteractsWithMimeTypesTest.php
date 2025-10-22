<?php

use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMimeTypes;

beforeEach(function () {
    $this->class = new class {
        use InteractsWithMimeTypes;

        public array $options = [];

        public function getOption($key)
        {
            return $this->options[$key] ?? null;
        }

        public function setOption($key, $value)
        {
            $this->options[$key] = $value;
        }

        public function hasCollection($name)
        {
            return in_array($name, ['image', 'document']);
        }

        // Public proxies to test protected methods
        public function callStringToMimeArray($input)
        {
            return $this->stringToMimeArray($input);
        }

        public function callMimeArrayToString($input)
        {
            return $this->mimeArrayToString($input);
        }

        public function callGetAllowedMimeTypes()
        {
            return $this->getAllowedMimeTypes();
        }

        public function callGetAllowedMimeTypesHuman(array $mimes)
        {
            return $this->getAllowedMimeTypesHuman($mimes);
        }

        public function callResolveAllowedMimeTypes()
        {
            return $this->resolveAllowedMimeTypes();
        }

        public function callSyncAllowedMimeTypes(array &$config)
        {
            return $this->syncAllowedMimeTypes($config);
        }
    };
});


it('converts comma-separated string to array', function () {
    expect($this->class->callStringToMimeArray('jpg, png , pdf'))
        ->toBe(['jpg', 'png', 'pdf']);
});

it('converts array to array with trimmed unique values', function () {
    expect($this->class->callStringToMimeArray(['jpg', 'png', 'jpg ', '']))
        ->toBe(['jpg', 'png']);
});

it('converts array to comma-separated string', function () {
    expect($this->class->callMimeArrayToString(['jpg', ' png', 'jpg']))
        ->toBe('jpg, png');
});

it('converts string to normalized comma-separated string', function () {
    expect($this->class->callMimeArrayToString('jpg, png , jpg'))
        ->toBe('jpg, png');
});

it('returns allowed MIME types from options', function () {
    $this->class->setOption('allowedMimeTypes', 'jpg, png');
    expect($this->class->callGetAllowedMimeTypes())->toBe(['jpg', 'png']);
});

it('resolves allowed MIME types to both formats', function () {
    $this->class->setOption('allowedMimeTypes', 'jpg, png');
    $resolved = $this->class->callResolveAllowedMimeTypes();

    expect($resolved['allowedMimeTypes'])->toBe('jpg, png');
    // Assuming mle_human_mimetype_label just uppercases for test
    expect($resolved['allowedMimeTypesHuman'])->toBe('jpg, png');
});

it('syncs allowed MIME types into config array', function () {
    $config = ['allowedMimeTypes' => 'jpg, png, jpg'];
    $this->class->callSyncAllowedMimeTypes($config);

    expect($config['allowedMimeTypes'])->toBe('jpg, png');
    expect($config['allowedMimeTypesHuman'])->toBe('jpg, png');
});

it('handles empty input gracefully', function () {
    expect($this->class->callStringToMimeArray(null))->toBe([]);
    expect($this->class->callMimeArrayToString(null))->toBe('');
});
