<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Mlbrgn\MediaLibraryExtensions\Exceptions\InvalidModelTypeException;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

it('resolves an existing model instance', function () {
    $model = $this->getTestBlogModel();
    $service = app(MediaService::class);

    $resolved = $service->findMediaModel(Blog::class, $model->id, 'default');

    expect($resolved)->toBeInstanceOf(Blog::class)
        ->and($resolved->id)->toBe($model->id);
});

it('throws 400 if model class does not exist', function () {
    $service = app(MediaService::class);

    $service->findMediaModel('NonExistentClass', '1', 'default');
})->throws(InvalidModelTypeException::class);

it('throws ModelNotFoundException if id not found', function () {
    $service = app(MediaService::class);

    $service->findMediaModel(Blog::class, '999', 'default');
})->throws(ModelNotFoundException::class);

it('throws exception if model does not implement HasMediaExtended', function () {
    $service = app(MediaService::class);

    // Simple anonymous class doesn't implement HasMediaExtended
    $class = new class extends Model {};
    $className = get_class($class);

    $service->findMediaModel($className, 1, 'default');
})->throws(InvalidModelTypeException::class, 'must implement Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended');

beforeEach(function () {
    Config::set('medialibrary-extensions.allowed_mimetypes.image', ['image/jpeg', 'image/png']);
    Config::set('medialibrary-extensions.allowed_mimetypes.document', ['application/pdf']);
});

it('returns image collection if mime type is in image list', function () {
    $file = UploadedFile::fake()->image('photo.jpg');
    //    request()->merge(['image_collection' => 'images']);
    request()->merge(['collections' => ['image' => 'images']]);

    $service = app(MediaService::class);
    $collectionType = $service->determineCollectionType($file);

    expect($collectionType)->toBe('image');
});

it('returns document collection if mime type is in document list', function () {
    $file = UploadedFile::fake()->create('file.pdf', 100, 'application/pdf');
    //    request()->merge(['document_collection' => 'docs']);
    request()->merge(['collections' => ['document' => 'document_collections']]);

    $service = app(MediaService::class);
    $collectionType = $service->determineCollectionType($file);

    expect($collectionType)->toBe('document');
});

it('returns null if mime type is not supported', function () {
    $file = UploadedFile::fake()->create('file.txt', 10, 'text/plain');

    $service = app(MediaService::class);
    $collection = $service->determineCollectionType($file);

    expect($collection)->toBeNull();
});

it('resolves an actual HasMedia model instance', function () {
    $model = $this->getTestBlogModel();

    $service = app(MediaService::class);
    $resolvedModel = $service->resolveModelOrClassName($model, 'default');

    expect($resolvedModel->model)->toBe($model);
    expect($resolvedModel->modelType)->toBe($model->getMorphClass());
    expect($resolvedModel->modelId)->toBe($model->getKey());
    expect($resolvedModel->temporaryUploadMode)->toBeFalse();
});

it('resolves a class name string that implements HasMedia', function () {
    $model = $this->getTestBlogModel();

    $service = app(MediaService::class);
    $resolvedModel = $service->resolveModelOrClassName($model->getMorphClass(), 'default');

    expect($resolvedModel->model)->toBeNull();
    expect($resolvedModel->modelType)->toBe($model->getMorphClass());
    expect($resolvedModel->modelId)->toBeNull();
    expect($resolvedModel->temporaryUploadMode)->toBeTrue();
});

it('throws InvalidArgumentException for non-existing class name', function () {
    $service = app(MediaService::class);
    $service->resolveModelOrClassName('NonExistentClass', 'default');
})->throws(InvalidArgumentException::class);

it('throws UnexpectedValueException if class does not implement HasMedia', function () {
    $service = app(MediaService::class);
    $service->resolveModelOrClassName(stdClass::class, 'default');
})->throws(UnexpectedValueException::class);

it('throws TypeError for invalid type', function () {
    $service = app(MediaService::class);
    $service->resolveModelOrClassName(123, 'default');
})->throws(InvalidArgumentException::class);
