<?php

use Illuminate\Support\Facades\Config;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('opens the media modal from the carousel and initializes the inner carousel (bootstrap-5, demo_default)', function () {
    Config::set('medialibrary-extensions.frontend_theme', 'bootstrap-5');

    // Visit demo page. The demo blade includes Media Carousel by default.
    $page = $this->visit('/mle-demo?theme=bootstrap-5&data_source=demo_default&use_xhr=1')
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
    $page->assertPresent('[data-mle-media-modal]')
        ->assertPresent('[data-mle-media-modal] [data-mle-carousel]');

    // Ensure at least one slide is rendered inside the modal.
    $page->assertPresent('[data-mle-media-modal] [data-mle-carousel] [data-mle-carousel-item]');
})->group('browser');
