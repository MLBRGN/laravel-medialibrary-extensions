<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Interfaces;

use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\MediaLibrary\HasMedia;

interface HasMediaExtended extends HasMedia {
    public static function allowsMediaUploads(): bool;

    public function allowsMediaUploadFrom(?Authenticatable $user): bool;

    public function allowedMediaCollections(): array;

    public static function allowsMediaDeletes(): bool;

    public function allowsMediaDeletesFrom(?Authenticatable $user): bool;

    public static function allowsMediaEdits(): bool;

    public function allowsMediaEditsFrom(?Authenticatable $user): bool;

}
