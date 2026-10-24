<?php

/** @noinspection InvalidDatasetNameCaseInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('can control standalone media carousel', function ($theme, $dataSource, $xhr, $temporary = false) {

    // prepare MMM selectors to upload media first
    $mmmPermanentId = '#alien-multiple-permanent-mmm';
    $mmmPermanentInputSelector = $mmmPermanentId.' [data-mle-media-input]';
    $mmmPermanentUploadButtonSelector = $mmmPermanentId.' [data-mle-media-upload-button]';

    $mmmTemporaryId = '#alien-multiple-temporary-mmm';
    $mmmTemporaryInputSelector = $mmmTemporaryId.' [data-mle-media-input]';
    $mmmTemporaryUploadButtonSelector = $mmmTemporaryId.' [data-mle-media-upload-button]';

    // prepare carousel selectors
    $carouselId = '#alien-carousel-crs';
    $indicatorsSelector = $carouselId.' [data-mle-carousel-indicators]';
    $nextButtonSelector = $carouselId.' [data-mle-carousel-next]';
    $prevButtonSelector = $carouselId.' [data-mle-carousel-prev]';
    $firstItemSelector = $carouselId.' [data-mle-carousel-item]:first-child';
    $secondItemSelector = $carouselId.' [data-mle-carousel-item]:nth-child(2)';

    // modal selectors
    $modalBaseId = 'alien-carousel-mod';
    $modalSelector = $this->getModalSelector($theme, $modalBaseId);

    $innerCarouselId = '#alien-carousel-mod-crs';
    $innerFirstItemSelector = $innerCarouselId.' [data-mle-carousel-item]:first-child';
    $innerSecondItemSelector = $innerCarouselId.' [data-mle-carousel-item]:nth-child(2)';
    $innerNextButtonSelector = $innerCarouselId.' [data-mle-carousel-next]';
    $innerPrevButtonSelector = $innerCarouselId.' [data-mle-carousel-prev]';

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    $dataSourceResolver = app(DataSourceResolver::class);
    $resolvedConnection = $dataSourceResolver->resolveConnection($dataSource);

    $this->assertDatabaseCount('media', 0, $resolvedConnection);

    $section = $temporary ? 'mmm-temporary,carousel' : 'mmm-permanent,carousel';
    $page = $this->visit("/mlbrgn-mle/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt&section=$section")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $carouselId);

    if (! $temporary) {
        $this->scrollIntoView($page, $mmmPermanentId);

        // 1. Upload two images via MMM
        $page->attach($mmmPermanentInputSelector, $this->getRandomFixture())
            ->press($mmmPermanentUploadButtonSelector)
            ->assertSee(__('medialibrary-extensions::messages.upload_success'));

        $page->attach($mmmPermanentInputSelector, $this->getRandomFixture())
            ->press($mmmPermanentUploadButtonSelector)
            ->assertSee(__('medialibrary-extensions::messages.upload_success'));

        $this->assertDatabaseCount('media', 2, $resolvedConnection);

    } else {
        $this->scrollIntoView($page, $mmmTemporaryId);

        // 1. Upload two images via MMM
        $page->attach($mmmTemporaryInputSelector, $this->getRandomFixture())
            ->press($mmmTemporaryUploadButtonSelector)
            ->assertSee(__('medialibrary-extensions::messages.upload_success'));

        $page->attach($mmmTemporaryInputSelector, $this->getRandomFixture())
            ->press($mmmTemporaryUploadButtonSelector)
            ->assertSee(__('medialibrary-extensions::messages.upload_success'));

        $this->assertDatabaseCount('mle_temporary_uploads', 2, $resolvedConnection);
    }

    // 2. Refresh the page to see them in Carousel
    $page->refresh();

    $this->scrollIntoView($page, $carouselId);

    // check that media still exists after refresh
    if (! $temporary) {
        $this->assertDatabaseCount('media', 2, $resolvedConnection);
    } else {
        $this->assertDatabaseCount('mle_temporary_uploads', 2, $resolvedConnection);
    }

    $page->assertPresent($carouselId)
        ->assertPresent($carouselId)
        ->assertPresent($indicatorsSelector)
        ->assertPresent($nextButtonSelector)
        ->assertPresent($prevButtonSelector)
        ->assertPresent($firstItemSelector)
        ->assertAttributeContains($firstItemSelector, 'class', 'active')

        // click next
        ->click($nextButtonSelector)
        ->assertAttributeContains($secondItemSelector, 'class', 'active')
        ->assertAttributeDoesntContain($firstItemSelector, 'class', 'active')

        // click prev
        ->click($prevButtonSelector)
        ->assertAttributeContains($firstItemSelector, 'class', 'active')
        ->assertAttributeDoesntContain($secondItemSelector, 'class', 'active')

        // click the indicator for the second item
        ->click($indicatorsSelector.' [data-mle-slide-to="1"]')
        ->assertAttributeContains($secondItemSelector, 'class', 'active')
        ->assertAttributeDoesntContain($firstItemSelector, 'class', 'active')

        // press the arrows
        ->keys($carouselId, 'ArrowLeft')
        ->assertAttributeContains($firstItemSelector, 'class', 'active')
        ->assertAttributeDoesntContain($secondItemSelector, 'class', 'active')

        ->keys($carouselId, 'ArrowRight')
        ->assertAttributeContains($secondItemSelector, 'class', 'active')
        ->assertAttributeDoesntContain($firstItemSelector, 'class', 'active')

        // test modal expansion if applicable (default is true)
        ->click($secondItemSelector)
        ->assertPresent($modalSelector)
        ->assertPresent($innerCarouselId)
        // Assert it opened on the second slide
        ->assertAttributeContains($innerSecondItemSelector, 'class', 'active')
        ->assertAttributeDoesntContain($innerFirstItemSelector, 'class', 'active')

        // click next in modal (wrap around since only 2 items)
        ->click($innerNextButtonSelector)
        ->assertAttributeContains($innerFirstItemSelector, 'class', 'active')
        ->assertAttributeDoesntContain($innerSecondItemSelector, 'class', 'active')

        // click prev in modal
        ->click($innerPrevButtonSelector)
        ->assertAttributeContains($innerSecondItemSelector, 'class', 'active')
        ->assertAttributeDoesntContain($innerFirstItemSelector, 'class', 'active')

        ->click($modalSelector.' '.$this->getModalCloseSelector($theme))
        ->assertMissing($modalSelector); // not visible

    $page->page()->close();
})->group('browser')
    ->with('media_carousel_test_matrix')
    ->flaky();
