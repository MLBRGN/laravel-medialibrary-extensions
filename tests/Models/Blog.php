<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mlbrgn\MediaLibraryExtensions\Tests\database\Factories\BlogFactory;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Traits\YouTubeCollection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Blog extends Model implements HasMedia
{
    use InteractsWithMediaExtended;
    use HasFactory;
    use YouTubeCollection;

    protected $guarded = [];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('blog-main')
            ->singleFile();

        $this
            ->addMediaCollection('blog-extra');

    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media) {
            $this->addResponsive16x9Conversion($media, [
                'blog-main',
                'blog-extra',
            ]);
        }
    }

    public static function newFactory(): BlogFactory
    {
        return BlogFactory::new();
    }
}
