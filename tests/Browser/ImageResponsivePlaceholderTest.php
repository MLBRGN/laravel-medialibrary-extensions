<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Tests\Browser;

use Mlbrgn\MediaLibraryExtensions\Tests\BrowserTestCase;

it('verifies all placeholder scenarios in the browser', function () {
    $this->visit('/test-placeholders')
        ->assertPresent('#attribute-placeholder img[src="/images/attr-placeholder.jpg"]')
        ->assertPresent('#model-placeholder img[src="/images/model-fallback.jpg"]')
        ->assertPresent('#config-placeholder img[src="/images/config-placeholder.jpg"]')
        ->assertPresent('#null-placeholder')
        ->assertMissing('#null-placeholder img')
        ->assertSourceMissing('id="Laag_2"'); // The ID of the SVG in no-media-icon.blade.php
})->group('browser');
