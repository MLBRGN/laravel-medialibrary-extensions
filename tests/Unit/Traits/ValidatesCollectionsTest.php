<?php

use Mlbrgn\MediaLibraryExtensions\Traits\ValidatesCollections;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

uses()->group('traits');

beforeEach(function () {
    // Anonymous class using the trait
    $this->class = new class {
        use ValidatesCollections;

        protected array $inputData = [];

        public function setInputData(array $data): void
        {
            $this->inputData = $data;
        }

        public function input(string $key, $default = null)
        {
            return $this->inputData[$key] ?? $default;
        }
    };

    // Create a real Validator
    $this->validator = ValidatorFacade::make([], []); // empty data & rules, we just need errors bag
});

it('passes validation with at least one allowed non-empty key', function () {
    $this->class->setInputData([
        'collections' => [
            'image' => 'file.jpg',
            'document' => '',
        ],
    ]);

    $this->class->addCollectionsValidation($this->validator);

    expect($this->validator->errors()->isEmpty())->toBeTrue();
});

it('adds error for invalid collection keys', function () {
    $this->class->setInputData([
        'collections' => [
            'image' => 'file.jpg',
            'invalidKey' => 'oops',
        ],
    ]);

    $this->class->addCollectionsValidation($this->validator);

    $errors = $this->validator->errors()->get('collections');
    expect($errors)->toContain('Invalid collection keys: invalidKey');
});

it('adds error if no non-empty allowed keys', function () {
    $this->class->setInputData([
        'collections' => [
            'image' => '',
            'document' => null,
            'youtube' => '',
        ],
    ]);

    $this->class->addCollectionsValidation($this->validator);

    $errors = $this->validator->errors()->get('collections');
    expect($errors)->toContain('At least one collection (image, document, audio, video, or youtube) must be set.');
});

it('adds both errors if invalid keys and empty allowed keys', function () {
    $this->class->setInputData([
        'collections' => [
            'foo' => 'bar',
            'image' => '',
        ],
    ]);

    $this->class->addCollectionsValidation($this->validator);

    $errors = $this->validator->errors()->get('collections');
    expect($errors)->toContain('Invalid collection keys: foo');
    expect($errors)->toContain('At least one collection (image, document, audio, video, or youtube) must be set.');
});

it('works when collections input is missing', function () {
    $this->class->setInputData([]);

    $this->class->addCollectionsValidation($this->validator);

    $errors = $this->validator->errors()->get('collections');
    expect($errors)->toContain('At least one collection (image, document, audio, video, or youtube) must be set.');
});
