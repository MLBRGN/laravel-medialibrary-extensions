<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mlbrgn\SpatieMediaLibraryExtensions\Traits\HandlesMediaConversions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Blog extends Model  implements HasMedia
{
    use HasFactory;
    use HandlesMediaConversions;

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
}
