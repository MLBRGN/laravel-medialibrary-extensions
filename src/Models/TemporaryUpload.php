<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Helpers\DemoHelper;
use Mlbrgn\MediaLibraryExtensions\Tests\Database\Factories\TemporaryUploadFactory;

class TemporaryUpload extends Model
{
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

    // null = default connection
    public function getConnectionName()
    {
        if (config('media-library-extensions.demo_pages_enabled') && DemoHelper::isRequestFromDemoPage()) {
            return config('media-library-extensions.demo_database_name');
        }

        return parent::getConnectionName();
    }

    //    public static function forCurrentSession($collectionName = null): Collection
    //    {
    //        return self::where('session_id', session()->getId())
    //            ->when(! is_null($collectionName), function ($query) use ($collectionName) {
    //                return $query->where('collection_name', $collectionName);
    //            })
    //            ->orderBy('order_column', 'asc')
    //            ->get();
    //    }

    public function scopeForCurrentSession($query, ?string $collectionName = null, ?string $instanceId = null)
    {
        Log::info('scopeForCurrentSession instanceId '.$instanceId);
        $query->when($instanceId, fn ($q) => $q->where('instance_id', $instanceId))
            ->unless($instanceId, fn ($q) => $q->where('session_id', session()->getId()))
            ->when($collectionName, fn ($q) => $q->where('collection_name', $collectionName))
            ->orderBy('order_column', 'asc');

        return $query->get();
    }
    //    public function scopeForCurrentSession($query, ?string $collectionName = null, ?string $instanceId = null)
    //    {
    //        $instanceId = $instanceId ?? request()->input('instance_id');
    //
    //        $query->when($instanceId, fn($q) => $q->where('instance_id', $instanceId))
    //            ->unless($instanceId, fn($q) => $q->where('session_id', session()->getId()))
    //            ->when($collectionName, fn($q) => $q->where('collection_name', $collectionName))
    //            ->orderBy('order_column', 'asc');
    //
    //        return $query->get();
    //    }

    public static function getForCurrentSession(?string $collectionName = null, ?string $instanceId = null): Collection
    {
        Log::info('getForCurrentSession instanceId '.$instanceId);

        return static::forCurrentSession($collectionName, $instanceId); // ->get();
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
