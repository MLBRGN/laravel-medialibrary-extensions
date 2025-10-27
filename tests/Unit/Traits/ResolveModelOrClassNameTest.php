<?php

use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Illuminate\Database\Eloquent\Model;

uses()->group('traits');

beforeEach(function () {
    $this->class = new class {
        use ResolveModelOrClassName;

        public function callResolveModelOrClassName(Model|string $modelOrClassName) {
            return $this->resolveModelOrClassName($modelOrClassName);
        }
    };
});

it('resolves an actual HasMedia model instance', function () {
    $model = $this->getTestBlogModel();

    $this->class->callResolveModelOrClassName($model);

    expect($this->class->model)->toBe($model);
    expect($this->class->modelType)->toBe($model->getMorphClass());
    expect($this->class->modelId)->toBe($model->getKey());
    expect($this->class->temporaryUploadMode)->toBeFalse();
});

it('resolves a class name string that implements HasMedia', function () {
    $model = $this->getTestBlogModel();

    $this->class->callResolveModelOrClassName($model->getMorphClass());

    expect($this->class->model)->toBeNull();
    expect($this->class->modelType)->toBe($model->getMorphClass());
    expect($this->class->modelId)->toBeNull();
    expect($this->class->temporaryUploadMode)->toBeTrue();
});

it('throws InvalidArgumentException for non-existing class name', function () {
    $this->class->callResolveModelOrClassName('NonExistentClass');
})->throws(InvalidArgumentException::class);

it('throws UnexpectedValueException if class does not implement HasMedia', function () {
    $this->class->callResolveModelOrClassName(\stdClass::class);
})->throws(UnexpectedValueException::class);

it('throws TypeError for invalid type', function () {
    $this->class->callResolveModelOrClassName(123);
})->throws(InvalidArgumentException::class);
