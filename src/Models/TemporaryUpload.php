<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class TemporaryUpload extends Model
{
    protected $table = 'mle_temporary_uploads';

    protected $fillable = [
        'disk',
        'path',
        'name',
        'file_name',
        'collection_name',
        'mime_type',
        'session_id',
        'user_id',
        'extra_properties',
        'order_column',
    ];

    protected $casts = [
        'extra_properties' => 'array',
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

//    public function getNameAttribute(): string
//    {
//        return $this->file_name;
//    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function getFullUrl(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    // TODO use config values?
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isYouTubeVideo(): bool
    {
        return $this->hasExtraProperty('youtube-id');
    }

    // TODO use config values?
    public function isDocument(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf', 'text/plain', 'application/msword',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function hasExtraProperty(string $key): bool
    {
        return array_key_exists($key, $this->extra_properties ?? []);
    }

    public function getExtraProperty(string $key, mixed $default = null): mixed
    {
        return $this->extra_properties[$key] ?? $default;
    }

    // TODO. This wrapper exists because media library uses custom property and i use extra property
    public function getCustomProperty(string $key, mixed $default = null): mixed
    {
        return $this->getExtraProperty($key, $default);
    }

    public function getNameWithExtension(): string
    {
        return $this->name . '.' . pathinfo($this->file_name, PATHINFO_EXTENSION);
    }
}
