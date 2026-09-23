<?php

use Mlbrgn\MediaLibraryExtensions\Tests\Browser\Concerns\InteractsWithBlogIntegration;

uses(InteractsWithBlogIntegration::class);

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('verifies multi-manager isolation and validation', function ($theme, $dataSource, $xhr) {
    $xhrInt = $xhr ? 1 : 0;
    
    $this->ensureLabMedium($dataSource);
    
    // Visit isolation test page
    $page = $this->visit("/mle-demo?isolation_test=1&theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    // 1. Verify all components are present
    $page->assertPresent('#manager-a-mmm')
        ->assertPresent('#manager-b-mmm')
        ->assertPresent('#media-lab-a-lab');

    // 2. Initial state check - no media yet.
    // Manager A has minMediaCount=1, so it should fail validation if submitted now.
    $page->press('[data-test="btn-submit-isolation"]');
    
    $page->waitForText(__('medialibrary-extensions::messages.at_least_one_medium_required'));

    // 3. Upload to Manager A to satisfy validation
    $inputA = '#manager-a-mmm [data-mle-media-input]';
    $uploadBtnA = '#manager-a-mmm [data-mle-media-upload-button]';
    
    $page->assertPresent($inputA);
    $page->attach($inputA, $this->getRandomFixture())
        ->press($uploadBtnA);
    
    $page->waitForText(__('medialibrary-extensions::messages.upload_success'));

    // 4. Verify independent max validation (Manager B: max 2)
    $inputB = '#manager-b-mmm [data-mle-media-input]';
    $uploadBtnB = '#manager-b-mmm [data-mle-media-upload-button]';
    
    // Upload 3 items to Manager B - should be blocked by frontend, but we test backend if possible.
    // Actually, MLE frontend blocks it. Let's just upload 2 and then verify.
    for ($i = 0; $i < 2; $i++) {
        $page->attach($inputB, $this->getRandomFixture())
            ->press($uploadBtnB);
        $page->waitForText(__('medialibrary-extensions::messages.upload_success'));
    }
    
    // Now Manager B is at max.
    $page->assertPresent('#manager-b-mmm [data-mle-max-reached-alert]');
    
    // 5. Verify Media Lab isolation - interacting with Lab should work (XHR)
    $labDeleteBtn = '#media-lab-a-lab [data-mle-media-delete-button]';
    if ($page->script("document.querySelector('$labDeleteBtn') !== null")) {
        $page->press($labDeleteBtn);
        // We don't necessarily need to assert success here, just that it didn't trigger a form submit of the parent form.
        // If it did, we'd lose the current page state.
        $page->assertPresent('#isolation-form');
    }

    // 5. Submit the form and verify payload isolation
    $page->press('[data-test="btn-submit-isolation"]');
    
    $page->waitForText('Submitted Data');

    // Check submitted data
    $submittedDataJson = $page->text('[data-test="submitted-data"]');
    $submittedData = json_decode($submittedDataJson, true);

    // Should contain app fields
    expect($submittedData)->toHaveKey('app_field', 'app-value');
    
    // Should contain media counts
    expect($submittedData)->toHaveKey('manager_a_count', '1');
    expect($submittedData)->toHaveKey('manager_b_count', '2');
    
    // Should contain instance maps
    expect($submittedData)->toHaveKey('mle_instance_map');
    expect($submittedData['mle_instance_map'])->toHaveKey('manager_a_count');
    expect($submittedData['mle_instance_map'])->toHaveKey('manager_b_count');
    expect($submittedData['mle_instance_map'])->toHaveKey('media_lab_a');

    // CRITICAL: Should NOT contain isolated fields
    expect($submittedData)->not->toHaveKey('client_token');
    expect($submittedData)->not->toHaveKey('mle_instance_ids');
    expect($submittedData)->not->toHaveKey('mle_instance_id');
    
    // Verify specific hidden fields from partials are NOT present
    // (These usually come from upload-form or destroy-form)
    expect($submittedData)->not->toHaveKey('collection');
    expect($submittedData)->not->toHaveKey('media_id');

})->with([
    'bootstrap + xhr' => ['bootstrap-5', 'demo_default', true],
    'plain + xhr' => ['plain', 'demo_default', true],
]);

it('verifies isolation with JavaScript disabled', function () {
    // This is tricky to test with Playwright/Laravel Dusk directly as they rely on JS.
    // But we can simulate it by checking that the 'form' attribute is present even if JS fails to run.
    
    $page = $this->visit("/mle-demo?isolation_test=1&theme=plain&use_xhr=0")
        ->assertNoJavaScriptErrors();

    // Check that internal inputs HAVE the form attribute pointing to the isolation ID
    $isolationIdA = 'mle-isolated-manager-a';
    
    // We can use script to check attributes even if we "simulate" no-js behavior for the form submission
    $hasFormAttr = $page->script("document.querySelector('#manager-a-mmm input[type=file]').getAttribute('form') === '$isolationIdA'");
    expect($hasFormAttr)->toBeTrue();
});
