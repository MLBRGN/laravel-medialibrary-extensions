<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Models\demo;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;

class Alien extends Model implements HasMediaExtended
{
    use InteractsWithMediaExtended;

    protected $table = 'aliens';

    protected $guarded = [];

    protected $connection = 'media_demo';

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('alien-single-image')
            ->singleFile()
            ->useDisk(config('media-library-extensions.media_disks.demo'));

        $this
            ->addMediaCollection('alien-single-document')
            ->singleFile()
            ->useDisk(config('media-library-extensions.media_disks.demo'));

        $this
            ->addMediaCollection('alien-single-youtube-video')
            ->singleFile()
            ->useDisk(config('media-library-extensions.media_disks.demo'));

        $this
            ->addMediaCollection('alien-single-video')
            ->singleFile()
            ->useDisk(config('media-library-extensions.media_disks.demo'));

        $this
            ->addMediaCollection('alien-single-audio')
            ->singleFile()
            ->useDisk(config('media-library-extensions.media_disks.demo'));

        $this->addMediaCollection('alien-multiple-images')
            ->useDisk(config('media-library-extensions.media_disks.demo'));

        $this->addMediaCollection('alien-multiple-documents')
            ->useDisk(config('media-library-extensions.media_disks.demo'));

        $this->addMediaCollection('alien-multiple-youtube-videos')
            ->useDisk(config('media-library-extensions.media_disks.demo'));

        $this->addMediaCollection('alien-multiple-videos')
            ->useDisk(config('media-library-extensions.media_disks.demo'));

        $this->addMediaCollection('alien-multiple-audio')
            ->useDisk(config('media-library-extensions.media_disks.demo'));

        $this->addMediaCollection('alien-media-lab')
            ->useDisk(config('media-library-extensions.media_disks.demo'));

    }

//    public function getConnectionName(): string
//    {
//        if (config('media-library-extensions.demo_pages_enabled') && DemoHelper::isRequestFromDemoPage()) {
//            return config('media-library-extensions.demo_database_name'); // TODO rename config key to demo_database_name?
//        }
//
//        return config('database.default');
//    }

//    public function getConnectionName(): ?string
//    {
//        if (app()->bound('mle-demo-state')) {
//            return config('media-library-extensions.demo_database_name');
//        }
//        return parent::getConnectionName();
//    }

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
}
