<?php

use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\Icon;

it('detects when the Blade UI icon alias exists', function () {
    // Fake component alias
    Blade::component('some-icon', 'existing-icon');

    $component = new Icon(name: 'existing-icon');

    expect($component->iconExists)->toBeTrue()
        ->and($component->render())->toBeInstanceOf(\Illuminate\View\View::class);
});

it('detects when the Blade UI icon alias does not exist', function () {
    $component = new Icon(name: 'nonexistent-icon');

    expect($component->iconExists)->toBeFalse()
        ->and($component->render())->toBeInstanceOf(\Illuminate\View\View::class);
});
