<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class TemporaryUpload extends Model
{
    //    public static function booted()
    //    {
    //        static::retrieved(function ($model) {
    //            dump('retrieved model', $model->id, $model->getConnectionName());
    //        });
    //    }

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
        'custom_properties',
        'order_column',
    ];

    protected $casts = [
        'custom_properties' => 'array',
    ];

    // used when serializing
    protected $appends = ['url'];

    public static function isAvailable(): bool
    {
        $instance = new static;
        $connection = $instance->getConnectionName(); // null = default connection
        $table = $instance->getTable();

        return Schema::connection($connection)->hasTable($table);
    }

    public function getConnectionName()
    {
        if (config('media-library-extensions.demo_pages_enabled') && \Mlbrgn\MediaLibraryExtensions\Helpers\DemoHelper::isRequestFromDemoPage()) {
            return config('media-library-extensions.temp_database_name');
        }

        return parent::getConnectionName();
    }

    public static function forCurrentSession($collectionName = null): Collection
    {
        return self::where('session_id', session()->getId())
            ->when($collectionName, fn ($query) => $query->where('collection_name', $collectionName)
            )
            ->orderBy('order_column', 'asc')
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

    //    public function forgetCustomProperty(string $key): static
    //    {
    //        $customProperties = $this->custom_properties ?? [];
    //
    //        unset($customProperties[$key]);
    //
    //        $this->custom_properties = $customProperties;
    //
    //        return $this;
    //    }

    public function getNameWithExtension(): string
    {
        return $this->name.'.'.pathinfo($this->file_name, PATHINFO_EXTENSION);
    }
}
