<?php

namespace Mlbrgn\MediaLibraryExtensions\Providers;

//use App\Providers\EventServiceProvider;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;
use Mlbrgn\MediaLibraryExtensions\Listeners\MediaHasBeenAddedListener;

class MediaLibraryExtensionsEventServiceProvider extends EventServiceProvider
{
    protected $listen = [
        MediaHasBeenAddedEvent::class => [
            MediaHasBeenAddedListener::class,
        ],
    ];
}
