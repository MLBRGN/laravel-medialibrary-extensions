<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Models\demo;

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

class DemoTemporaryUpload extends TemporaryUpload
{
    protected $connection = 'media_demo';
}
