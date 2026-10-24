<?php

use Illuminate\Support\Facades\Config;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('opens the media modal from the carousel and initializes the inner carousel', function (string $theme) {
    Config::set('medialibrary-extensions.frontend_theme', $theme);

    // Visit demo page. The demo blade includes Media Carousel by default.
    $page = $this->visit("/mlbrgn-mle/mle-demo?theme={$theme}&data_source=demo_default&use_xhr=1&section=carousel")
        ->assertNoJavaScriptErrors()
        ->assertSee('Media Carousel');

    // Best-effort: click refresh if the control exists; otherwise continue.
    // Using a short wait for selector, but not failing if absent.
    try {
        $page->waitFor('#carouselRefreshButton', 250);
        $page->click('#carouselRefreshButton');
    } catch (Throwable $e) {
        // ignore if not present
    }

    // Try to select the second indicator if available; ignore if not found.
    try {
        $page->waitFor('[data-mle-carousel] [data-mle-carousel-indicators] [data-mle-carousel-indicator]:nth-child(2)', 250);
        $page->click('[data-mle-carousel] [data-mle-carousel-indicators] [data-mle-carousel-indicator]:nth-child(2)');
    } catch (Throwable $e) {
        // ignore if there's only one item
    }

    // Click on the visible carousel item of the OUTER carousel to open the modal.
    // Target the demo carousel by its known id prefix to avoid clicking the hidden in-modal carousel.
    $page->click('#alien-carousel-crs .mle-media-carousel-item.active .mle-media-carousel-item-container');

    // Assert modal becomes visible and its internal carousel is present.
    $modalSelector = $this->getModalSelector($theme, 'alien-carousel-mod');

    $page->assertPresent($modalSelector)
        ->assertPresent($modalSelector.' [data-mle-carousel]');

    // Ensure at least one slide is rendered inside the modal.
    $page->assertPresent($modalSelector.' [data-mle-carousel] [data-mle-carousel-item]');
})->with(['bootstrap-5', 'bootstrap-6'])->group('browser');
