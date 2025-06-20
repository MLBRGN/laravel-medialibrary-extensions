<?php

namespace Mlbrgn\MediaLibraryExtensions\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Aliens extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'aliens';
    protected $connection = 'media_demo';
    protected $guarded = [];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('alien-single-image')
            ->singleFile()
            ->useDisk('public');

        $this
            ->addMediaCollection('alien-single-document')
            ->singleFile()
            ->useDisk('public');

        $this
            ->addMediaCollection('alien-single-youtube-video')
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('alien-multiple-images')
            ->useDisk('public');

        $this->addMediaCollection('alien-multiple-documents')
            ->useDisk('public');

        $this->addMediaCollection('alien-multiple-youtube-videos')
            ->useDisk('public');

    }

}
