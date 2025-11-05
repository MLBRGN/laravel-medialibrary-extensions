<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Models\demo;

use Illuminate\Database\Eloquent\Model;
use Mlbrgn\MediaLibraryExtensions\Helpers\DemoHelper;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Alien extends Model implements HasMedia
{
    use InteractsWithMedia;
    use InteractsWithMediaExtended;

    protected $table = 'aliens';

    protected $guarded = [];

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
    }

    public function getConnectionName(): string
    {
        if (config('media-library-extensions.demo_pages_enabled') && DemoHelper::isRequestFromDemoPage()) {
            return config('media-library-extensions.demo_database_name');// TODO rename config key to demo_database_name?
        }

        return config('database.default');
    }
}
