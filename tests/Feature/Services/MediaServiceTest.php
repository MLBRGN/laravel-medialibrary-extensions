<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Mlbrgn\MediaLibraryExtensions\Exceptions\InvalidModelTypeException;
use Mlbrgn\MediaLibraryExtensions\Services\MediaCollectionService;
use Mlbrgn\MediaLibraryExtensions\Services\MediaModelResolver;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

it('resolves an existing model instance', function () {
    $model = $this->getTestBlogModel();
    $mediaModelResolver = app(MediaModelResolver::class);

    $resolved = $mediaModelResolver->resolveModelById(Blog::class, $model->id, 'default');

    expect($resolved)->toBeInstanceOf(Blog::class)
        ->and($resolved->id)->toBe($model->id);
});

it('throws 400 if model class does not exist', function () {
    $mediaModelResolver = app(MediaModelResolver::class);
    $mediaModelResolver->resolveModelById('NonExistentClass', '1', 'default');
})->throws(InvalidModelTypeException::class);

it('throws ModelNotFoundException if id not found', function () {
    $mediaModelResolver = app(MediaModelResolver::class);
    $mediaModelResolver->resolveModelById(Blog::class, '999', 'default');
})->throws(ModelNotFoundException::class);

it('throws exception if model does not implement HasMediaExtended', function () {
    $mediaModelResolver = app(MediaModelResolver::class);

    // Simple anonymous class doesn't implement HasMediaExtended
    $class = new class extends Model {};
    $className = get_class($class);

    $mediaModelResolver->resolveModelById($className, 1, 'default');
})->throws(InvalidModelTypeException::class, 'must implement Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended');

beforeEach(function () {
    Config::set('medialibrary-extensions.allowed_mimetypes.image', ['image/jpeg', 'image/png']);
    Config::set('medialibrary-extensions.allowed_mimetypes.document', ['application/pdf']);
});

it('returns image collection if mime type is in image list', function () {
    $file = UploadedFile::fake()->image('photo.jpg');
    //    request()->merge(['image_collection' => 'images']);
    request()->merge(['collections' => ['image' => 'images']]);

    $mediaCollectionService = app(MediaCollectionService::class);
    $collectionType = $mediaCollectionService->determineCollectionType($file);

    expect($collectionType)->toBe('image');
});

it('returns document collection if mime type is in document list', function () {
    $file = UploadedFile::fake()->create('file.pdf', 100, 'application/pdf');
    //    request()->merge(['document_collection' => 'docs']);
    request()->merge(['collections' => ['document' => 'document_collections']]);

    $mediaCollectionService = app(MediaCollectionService::class);
    $collectionType = $mediaCollectionService->determineCollectionType($file);

    expect($collectionType)->toBe('document');
});

it('returns null if mime type is not supported', function () {
    $file = UploadedFile::fake()->create('file.txt', 10, 'text/plain');

    $mediaCollectionService = app(MediaCollectionService::class);
    $collection = $mediaCollectionService->determineCollectionType($file);

    expect($collection)->toBeNull();
});

it('resolves an actual HasMedia model instance', function () {
    $model = $this->getTestBlogModel();

    $mediaModelResolver = app(MediaModelResolver::class);
    $resolvedModel = $mediaModelResolver->resolveModelReference($model, 'default');

    expect($resolvedModel->model)->toBe($model);
    expect($resolvedModel->modelType)->toBe(get_class($model));
    expect($resolvedModel->modelId)->toBe($model->getKey());
    expect($resolvedModel->temporaryUploadMode)->toBeFalse();
});

it('resolves a class name string that implements HasMedia', function () {
    $model = $this->getTestBlogModel();

    $mediaModelResolver = app(MediaModelResolver::class);
    $resolvedModel = $mediaModelResolver->resolveModelReference($model->getMorphClass(), 'default');

    expect($resolvedModel->model)->toBeNull();
    expect($resolvedModel->modelType)->toBe(get_class($model));
    expect($resolvedModel->modelId)->toBeNull();
    expect($resolvedModel->temporaryUploadMode)->toBeTrue();
});

it('throws InvalidArgumentException for non-existing class name', function () {
    $mediaModelResolver = app(MediaModelResolver::class);
    $mediaModelResolver->resolveModelReference('NonExistentClass', 'default');
})->throws(InvalidArgumentException::class);

it('throws UnexpectedValueException if class does not implement HasMedia', function () {
    $mediaModelResolver = app(MediaModelResolver::class);
    $mediaModelResolver->resolveModelReference(stdClass::class, 'default');
})->throws(UnexpectedValueException::class);

it('throws TypeError for invalid type', function () {
    $mediaModelResolver = app(MediaModelResolver::class);
    $mediaModelResolver->resolveModelReference(123, 'default');
})->throws(InvalidArgumentException::class);
