<?php

namespace Mlbrgn\MediaLibraryExtensions\Providers;

use App\Providers\EventServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;
use Mlbrgn\MediaLibraryExtensions\Listeners\CopyOriginalMediaListener;

class MediaLibraryExtensionsEventServiceProvider extends EventServiceProvider
{
    protected $listen = [
        MediaHasBeenAddedEvent::class => [
            CopyOriginalMediaListener::class,
        ],
    ];
}
