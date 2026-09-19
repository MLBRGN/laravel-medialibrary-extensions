<?php

/** @noinspection InvalidDatasetNameCaseInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Mlbrgn\MediaLibraryExtensions\Tests\Browser\Concerns\InteractsWithBlogIntegration;

uses(InteractsWithBlogIntegration::class);

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('validates minimum media requirements', function ($theme, $dataSource, $xhr, $storage) {
    $mediaManagerId = '#alien-multiple-min-media-mmm';
    $inputSelector = $mediaManagerId . ' [data-mle-media-input]';
    $uploadButtonSelector = $mediaManagerId . ' [data-mle-media-upload-button]';
    $countsSelector = $mediaManagerId . ' .mle-media-manager-media-counts';
    $requiredIndicatorSelector = $mediaManagerId . ' .mle-label .mle-required-indicator';
    $hiddenCountSelector = 'input[data-mle-media-count="alien-multiple-min-media"]';
    $submitButtonSelector = '[data-test="btn-save-min-media"]';
    $errorMessageSelector = '.mle-error-message';

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $mediaManagerId);

    // 1. Verify required indicator is present
    $page->assertPresent($requiredIndicatorSelector)
        ->assertSeeIn($requiredIndicatorSelector, '*');

    // 2. Verify hidden count is initially 0
    $page->assertValue($hiddenCountSelector, '0');

    // 3. Attempt to submit with 0 items - should fail validation
    $page->click($submitButtonSelector);
    
    if (!$xhr) {
        $page->wait($waitTime);
    }

    $page->assertSee(__('medialibrary-extensions::messages.this_collection_requires_at_least_:items_items', ['items' => 2]));

    // 4. Upload 1 item
    $page->attach($inputSelector, $this->getRandomFixture())
        ->click($uploadButtonSelector);

    if (!$xhr) {
        $page->wait($waitTime);
    }
    
    $page->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // 5. Verify hidden count is updated to 1
    $page->assertValue($hiddenCountSelector, '1');

    // 6. Verify required indicator still present after AJAX update
    $page->assertPresent($requiredIndicatorSelector);

    // 7. Attempt to submit with 1 item - should still fail validation
    $page->click($submitButtonSelector);
    
    if (!$xhr) {
        $page->wait($waitTime);
    }

    $page->assertSee(__('medialibrary-extensions::messages.this_collection_requires_at_least_:items_items', ['items' => 2]));

    // 8. Upload 2nd item
    $page->attach($inputSelector, $this->getRandomFixture())
        ->click($uploadButtonSelector);

    if (!$xhr) {
        $page->wait($waitTime);
    }
    
    $page->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // 9. Verify hidden count is updated to 2
    $page->assertValue($hiddenCountSelector, '2');

    // 10. Submit with 2 items - should succeed
    $page->click($submitButtonSelector);
    
    if (!$xhr) {
        $page->wait($waitTime);
    }

    // Success in demo means redirecting back to index (potentially with an 'id' in query)
    // and NOT seeing the error message anymore.
    $page->assertDontSee(__('medialibrary-extensions::messages.this_collection_requires_at_least_:items_items', ['items' => 2]));

})->with([
    'bootstrap + demo default + xhr' => ['bootstrap-5', 'demo_default', true, 'permanent'],
    'plain + demo default + xhr' => ['plain', 'demo_default', true, 'permanent'],
]);
