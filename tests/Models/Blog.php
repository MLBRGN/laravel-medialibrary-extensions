<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Tests\Factories\BlogFactory;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Blog extends Model implements HasMediaExtended
{
    use InteractsWithMediaExtended;

    /**
     * Names of HTML editor fields to be scanned for temporary URLs.
     * Declared as a real property so Eloquent does not try to persist it.
     * Tests may set this at runtime: $model->htmlEditorFields = ['content'].
     *
     * @var array<int, string>
     */
    public array $htmlEditorFields = [];

    protected $guarded = [];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('blog-main')
            ->singleFile();

        $this
            ->addMediaCollection('blog-gallery');

        $this
            ->addMediaCollection('blog-youtube')
            ->singleFile();

        $this
            ->addMediaCollection('blog-lab')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media) {
            $this->addResponsive16x9Conversion($media, [
                'blog-main',
                'blog-gallery',
                'blog-youtube',
                'blog-lab',
            ]);
        }
    }

    public static function newFactory(): BlogFactory
    {
        return BlogFactory::new();
    }
}
