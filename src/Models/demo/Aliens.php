<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Models\demo;

use Illuminate\Database\Eloquent\Model;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Traits\YouTubeCollection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Aliens extends Model implements HasMedia
{
    use InteractsWithMedia;
    use InteractsWithMediaExtended;
    use YouTubeCollection;

    protected $table = 'aliens';
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

    public function getConnectionName(): string
    {
        return config('media-library-extensions.temp_database_name');
    }

}
