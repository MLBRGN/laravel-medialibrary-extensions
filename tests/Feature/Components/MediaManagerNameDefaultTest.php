<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature\Components;

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManager;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerMultiple;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerSingle;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerTinymce;

it('defaults name to id for media manager', function () {
    $component = new MediaManager(
        id: 'my-manager',
        modelReference: Blog::class,
        collections: ['images']
    );

    expect($component->name)->toBe('my-manager');
});

it('allows overriding name for media manager', function () {
    $component = new MediaManager(
        id: 'my-manager',
        modelReference: Blog::class,
        collections: ['images'],
        name: 'custom-name'
    );

    expect($component->name)->toBe('custom-name');
});

it('defaults name to id for media manager single', function () {
    $component = new MediaManagerSingle(
        id: 'my-single-manager',
        modelReference: Blog::class,
        collections: ['image']
    );

    expect($component->name)->toBe('my-single-manager');
});

it('defaults name to id for media manager multiple', function () {
    $component = new MediaManagerMultiple(
        id: 'my-multiple-manager',
        modelReference: Blog::class,
        collections: ['images']
    );

    expect($component->name)->toBe('my-multiple-manager');
});

it('defaults name to id for media manager tinymce', function () {
    $component = new MediaManagerTinymce(
        id: 'my-tinymce-manager',
        modelReference: Blog::class,
        collections: ['images']
    );

    expect($component->name)->toBe('my-tinymce-manager');
});
