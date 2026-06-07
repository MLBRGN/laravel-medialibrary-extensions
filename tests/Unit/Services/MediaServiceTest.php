<?php

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Mlbrgn\MediaLibraryExtensions\Exceptions\InvalidModelTypeException;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\FileAdder;
use Spatie\MediaLibrary\MediaCollections\FileAdderFactory;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('resolves an existing model instance', function () {
    $model = $this->getTestBlogModel();
    $service = app(MediaService::class);

    $resolved = $service->findMediaModel(Blog::class, $model->id);

    expect($resolved)->toBeInstanceOf(Blog::class)
        ->and($resolved->id)->toBe($model->id);
});

it('throws 400 if model class does not exist', function () {
    $service = app(MediaService::class);

    $service->findMediaModel('NonExistentClass', '1');
})->throws(InvalidModelTypeException::class);

it('throws ModelNotFoundException if id not found', function () {
    $service = app(MediaService::class);

    $service->findMediaModel(Blog::class, '999');
})->throws(ModelNotFoundException::class);

it('throws exception if model does not implement HasMediaExtended', function () {
    $service = app(MediaService::class);

    // Simple anonymous class doesn't implement HasMediaExtended
    $class = new class extends Model {};
    $className = get_class($class);

    $service->findMediaModel($className, 1);
})->throws(InvalidModelTypeException::class, 'must implement Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended');

it('throws exception if model does not use InteractsWithMediaExtended trait', function () {
    $service = app(MediaService::class);

    // This class implements the interface but doesn't use the trait
    $class = new class extends Model implements HasMediaExtended
    {
        public static function allowsMediaUploads(): bool
        {
            return true;
        }

        public function allowsMediaUploadFrom(?Authenticatable $user): bool
        {
            return true;
        }

        public function allowedMediaCollections(): array
        {
            return [];
        }

        public static function allowsMediaDeletes(): bool
        {
            return true;
        }

        public function allowsMediaDeletesFrom(?Authenticatable $user): bool
        {
            return true;
        }

        public static function allowsMediaEdits(): bool
        {
            return true;
        }

        public function allowsMediaEditsFrom(?Authenticatable $user): bool
        {
            return true;
        }

        public function registerMediaCollections(): void {}

        public function registerMediaConversions(?Media $media = null): void {}

        public function media(): MorphMany
        {
            return $this->morphMany(config('media-library.media_model'), 'model');
        }

        public function addMedia($file): FileAdder
        {
            return app(FileAdderFactory::class)->create($this, $file);
        }

        public function copyMedia($file): FileAdder
        {
            return app(FileAdderFactory::class)->createAllFromDisk($this, $file);
        }

        public function hasMedia(string $collectionName = ''): bool
        {
            return false;
        }

        public function getMedia(string $collectionName = 'default', $filters = []): Collection
        {
            return collect();
        }

        public function clearMediaCollection(string $collectionName = 'default'): HasMedia
        {
            return $this;
        }

        public function clearMediaCollectionExcept(string $collectionName = 'default', $excludedMedia = []): HasMedia
        {
            return $this;
        }

        public function getRegisteredMediaCollections(): Collection
        {
            return collect();
        }

        public function getRegisteredMediaConversions(): Collection
        {
            return collect();
        }

        public function getFallbackPaths(string $collectionName = 'default', string $conversionName = ''): array
        {
            return [];
        }

        public function getFallbackUrls(string $collectionName = 'default', string $conversionName = ''): array
        {
            return [];
        }

        public function shouldDeletePreservingMedia(): bool
        {
            return false;
        }

        public function loadMedia(string $collectionName): Illuminate\Database\Eloquent\Collection
        {
            return new Illuminate\Database\Eloquent\Collection;
        }

        public function addMediaConversion(string $name): Conversion
        {
            return new Conversion($name);
        }

        public function registerAllMediaConversions(): void {}

        public function getMediaCollection(string $collectionName = 'default'): ?MediaCollection
        {
            return null;
        }

        public function getMediaModel(): string
        {
            return config('media-library.media_model');
        }
    };
    $className = get_class($class);

    $service->findMediaModel($className, 1);
})->throws(InvalidModelTypeException::class, 'must use trait Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended');

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
