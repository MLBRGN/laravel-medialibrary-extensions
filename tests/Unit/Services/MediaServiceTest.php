<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

it('resolves an existing model instance', function () {
    $model = $this->getTestBlogModel();
    $service = new MediaService;

    $resolved = $service->resolveModel(Blog::class, $model->id);

    expect($resolved)->toBeInstanceOf(Blog::class)
        ->and($resolved->id)->toBe($model->id);
});

it('throws 400 if model class does not exist', function () {
    $service = new MediaService;

    $service->resolveModel('NonExistentClass', '1');
})->throws(\Exception::class, 'Invalid model type');

it('throws ModelNotFoundException if id not found', function () {
    $service = new MediaService;

    $service->resolveModel('\App\Models\XFiles', '999');
})->throws(Exception::class);

beforeEach(function () {
    Config::set('media-library-extensions.allowed_mimetypes.image', ['image/jpeg', 'image/png']);
    Config::set('media-library-extensions.allowed_mimetypes.document', ['application/pdf']);
});

it('returns image collection if mime type is in image list', function () {
    $file = UploadedFile::fake()->image('photo.jpg');
//    request()->merge(['image_collection' => 'images']);
    request()->merge(['collections' => ['image' => 'images']]);

    $service = new MediaService;
    $collectionType = $service->determineCollectionType($file);
//    $collectionName =

    expect($collectionType)->toBe('image');
//    expect($collectionName)->toBe('images');
});

it('returns document collection if mime type is in document list', function () {
    $file = UploadedFile::fake()->create('file.pdf', 100, 'application/pdf');
//    request()->merge(['document_collection' => 'docs']);
    request()->merge(['collections' => ['document' => 'document_collections']]);

    $service = new MediaService;
    $collectionType = $service->determineCollectionType($file);

    expect($collectionType)->toBe('document');
});

it('returns null if mime type is not supported', function () {
    $file = UploadedFile::fake()->create('file.txt', 10, 'text/plain');

    $service = new MediaService;
    $collection = $service->determineCollectionType($file);

    expect($collection)->toBeNull();
});
