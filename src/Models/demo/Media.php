<?php

namespace Mlbrgn\MediaLibraryExtensions\Models\demo;

use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
//    protected $connection = 'media_demo';
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
