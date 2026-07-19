<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Database\Factories\TemporaryUploadFactory;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended;

class TemporaryUpload extends Model implements HasMediaExtended
{
    use InteractsWithMediaExtended;

    public static function newFactory()
    {
        return TemporaryUploadFactory::new();
    }

    protected $table = 'mle_temporary_uploads';

    protected $fillable = [
        'disk',
        'path',
        'name',
        'file_name',
        'collection_name',
        'mime_type',
        'size',
        'client_token',
        'user_id',
        'instance_id',
        'custom_properties',
        'order_column',
    ];

    protected $casts = [
        'custom_properties' => 'array',
    ];

    // used when serializing
    protected $appends = ['url'];

    public function scopeForDataSource($query, ?string $dataSource = 'default')
    {
        if ($dataSource) {
            $connection = app(DataSourceResolver::class)
                ->resolveConnection($dataSource);

            $query->getQuery()->connection = app('db')->connection($connection);
            $query->getModel()->setConnection($connection);
        }

        return $query;
    }

    public function scopeForCurrentClient(
        $query,
        ?string $clientToken = null
    ) {
        $clientToken ??= request()->input('client_token')
            ?: request()->cookie('mle_client_token');

        if (! $clientToken && app()->runningUnitTests()) {
            $clientToken = config('medialibrary-extensions.test_client_token');
        }

        if (! $clientToken) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where('client_token', $clientToken)
            ->orderBy('order_column');
    }

    public function scopeForInstance(
        $query,
        ?string $instanceId
    ) {
        return $instanceId
            ? $query->where('instance_id', $instanceId)
            : $query;
    }

    public function scopeForCollection(
        $query,
        mixed $collectionName
    ) {
        return is_null($collectionName)
            ? $query
            : $query->where('collection_name', $collectionName);
    }

    public function scopeForCollections(
        $query,
        array $collectionNames
    ) {
        return $query->whereIn('collection_name', $collectionNames);
    }

    public static function getForCurrentClient(
        string|array|null $collectionNames = null,
        ?string $instanceId = null,
        ?string $dataSource = 'default',
        ?string $clientToken = null,
    ): Collection {
        $query = static::query()
            ->forDataSource($dataSource)
            ->forCurrentClient($clientToken)
            ->forInstance($instanceId);

        if (is_array($collectionNames)) {
            return $query
                ->forCollections($collectionNames)
                ->get();
        }

        return $query
            ->forCollection($collectionNames)
            ->get();
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function getUrl(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function getCustomProperty(string $key, mixed $default = null): mixed
    {
        return $this->custom_properties[$key] ?? $default;
    }

    public function hasCustomProperty(string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $this->custom_properties ?? []);
    }

    public function setCustomProperty(string $key, mixed $value): static
    {
        $customProperties = $this->custom_properties ?? [];

        $customProperties[$key] = $value;

        $this->custom_properties = $customProperties;

        return $this;
    }

    public function getNameWithExtension(): string
    {
        return $this->name.'.'.pathinfo($this->file_name, PATHINFO_EXTENSION);
    }
}
