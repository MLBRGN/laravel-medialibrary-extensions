<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;
use Mlbrgn\MediaLibraryExtensions\Tests\Database\Factories\TemporaryUploadFactory;
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

    public function scopeForCurrentClient($query, mixed $collectionName = null, ?string $instanceId = null, ?string $clientToken = null)
    {
        $clientToken = $clientToken ?: (request()->input('client_token') ?: request()->cookie('mle_client_token'));

        // TODO needed?
        if (! $clientToken && app()->runningUnitTests()) {
            // We use a stable fallback for unit tests to avoid breaking them
            // when no explicit token is provided.
            $clientToken = config('medialibrary-extensions.test_client_token');
        }

        if (! $clientToken) {
            // If no token is provided, we return no results to prevent cross-visitor leakage
            return $query->whereRaw('1 = 0');
        }

        $query
            ->where('client_token', $clientToken)
            ->when($instanceId, fn ($q) => $q->where('instance_id', $instanceId))
            ->when(! is_null($collectionName), fn ($q) => $q->where('collection_name', $collectionName))
            ->orderBy('order_column', 'asc');

        return $query;
    }

    public static function getForCurrentClient(mixed $collectionName = null, ?string $instanceId = null, ?string $dataSource = 'default', ?string $clientToken = null): Collection
    {
        return static::query()
            ->forDataSource($dataSource)
            ->forCurrentClient($collectionName, $instanceId, $clientToken)
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
