<?php

namespace Mlbrgn\MediaLibraryExtensions\Providers;

// use App\Providers\EventServiceProvider;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Mlbrgn\MediaLibraryExtensions\Listeners\MediaHasBeenAddedListener;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class MediaLibraryExtensionsEventServiceProvider extends EventServiceProvider
{
    protected $listen = [
        MediaHasBeenAddedEvent::class => [
            MediaHasBeenAddedListener::class,
        ],
    ];
}
