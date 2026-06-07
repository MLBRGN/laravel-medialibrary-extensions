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
        'session_id',
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

    public function getConnectionName(): ?string
    {
        //        if (app()->bound('mle-demo-mode')) {
        //            return config('medialibrary-extensions.demo_database_name');
        //        }

        return parent::getConnectionName();
    }

    public function scopeForDataSource($query, ?string $dataSource = null)
    {
        if ($dataSource) {
            $connection = app(DataSourceResolver::class)
                ->resolveConnection($dataSource);

            $query->getQuery()->connection = app('db')->connection($connection);
        }

        return $query;
    }

    public function scopeForCurrentSession($query, mixed $collectionName = null, ?string $instanceId = null, ?string $sessionId = null)
    {
        $sessionId = $sessionId ?: session()->getId();
        // Log::info('scopeForCurrentSession - sessionId: '.$sessionId.' instanceId: '.$instanceId.' collectionName: '.$collectionName);
        $query->where('session_id', $sessionId)
            ->when($instanceId, fn ($q) => $q->where('instance_id', $instanceId))
            ->when(! is_null($collectionName), fn ($q) => $q->where('collection_name', $collectionName))
            ->orderBy('order_column', 'asc');

        return $query;
    }

    public static function getForCurrentSession(mixed $collectionName = null, ?string $instanceId = null, ?string $dataSource = null, ?string $sessionId = null): Collection
    {
        return static::query()
            ->forDataSource($dataSource)
            ->forCurrentSession($collectionName, $instanceId, $sessionId)
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
