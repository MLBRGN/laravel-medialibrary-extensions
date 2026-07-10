<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Models\demo;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended;

class Alien extends Model implements HasMediaExtended
{
    use InteractsWithMediaExtended;

    protected $table = 'aliens';

    protected $guarded = [];

    // protected $connection = 'media_demo';

    public function registerMediaCollections(): void
    {
//        Log::info('Registered media collections', [
//            'collections' => collect($this->mediaCollections)
//                ->pluck('name')
//                ->values()
//                ->toArray(),
//        ]);


        $this
            ->addMediaCollection('alien-single-image')
            ->singleFile()
            ->useDisk(PackageInfrastructure::disk('demo'));

        $this
            ->addMediaCollection('alien-single-document')
            ->singleFile()
            ->useDisk(PackageInfrastructure::disk('demo'));

        $this
            ->addMediaCollection('alien-single-youtube-video')
            ->singleFile()
            ->useDisk(PackageInfrastructure::disk('demo'));

        $this
            ->addMediaCollection('alien-single-video')
            ->singleFile()
            ->useDisk(PackageInfrastructure::disk('demo'));

        $this
            ->addMediaCollection('alien-single-audio')
            ->singleFile()
            ->useDisk(PackageInfrastructure::disk('demo'));

        $this->addMediaCollection('alien-multiple-images')
            ->useDisk(PackageInfrastructure::disk('demo'));

        $this->addMediaCollection('alien-multiple-documents')
            ->useDisk(PackageInfrastructure::disk('demo'));

        $this->addMediaCollection('alien-multiple-youtube-videos')
            ->useDisk(PackageInfrastructure::disk('demo'));

        $this->addMediaCollection('alien-multiple-videos')
            ->useDisk(PackageInfrastructure::disk('demo'));

        $this->addMediaCollection('alien-multiple-audio')
            ->useDisk(PackageInfrastructure::disk('demo'));

        $this->addMediaCollection('alien-media-lab')
            ->useDisk(PackageInfrastructure::disk('demo'));

        $this
            ->addMediaCollection('alien-media-html-editor')
            ->useDisk(PackageInfrastructure::disk('demo'));
    }

    public static function allowsMediaUploads(): bool
    {
        return true;
    }

    public function allowedMediaCollections(): array
    {
        return [];
    }

    public function allowsMediaUploadFrom(?Authenticatable $user): bool
    {
        return true;
    }

    public function allowsMediaDeletesFrom(?Authenticatable $user): bool
    {
        return true;
    }

    public function allowsMediaEditsFrom(?Authenticatable $user): bool
    {
        return true;
    }

    public static function allowsMediaDeletes(): bool
    {
        return true;
    }

    public static function allowsMediaEdits(): bool
    {
        return true;
    }
}
