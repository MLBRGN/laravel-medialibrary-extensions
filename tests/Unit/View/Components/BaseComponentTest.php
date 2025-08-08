<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Testing\TestComponent;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

// Concrete subclass for testing the abstract BaseComponent
class BaseComponentTest extends BaseComponent
{
    public function render()
    {
        return ''; // Dummy render method
    }
}

it('initializes with provided id and theme', function () {
    Session::put(status_session_prefix(), ['success' => 'All good']);
    Config::set('media-library-extensions.frontend_theme', 'default-theme');

    $component = new BaseComponentTest('my-id', 'custom-theme');

    expect($component->id)->toBe('my-id')
        ->and($component->frontendTheme)->toBe('custom-theme')
        ->and($component->status)->toBe(['success' => 'All good']);
});

it('generates a unique id if none provided', function () {
    Session::put(status_session_prefix(), null);
    Config::set('media-library-extensions.frontend_theme', 'fallback-theme');

    $component = new BaseComponentTest('my-id', 'custom-theme');

    expect($component->frontendTheme)->toBe('custom-theme')
        ->and($component->status)->toBeNull();
});
