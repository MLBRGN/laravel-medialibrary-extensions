<?php

use Mlbrgn\MediaLibraryExtensions\Support\ClientContext;
use Pest\Browser\Api\AwaitableWebpage;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('persists client_token in session/cookies and reuses it', function () {
    // 1. Visit test route to generate token
    $page = $this->visit('/test-client-token');
    $json = json_decode($page->page()->locator('body')->innerText(), true);
    $firstToken = $json['token'];
    
    expect($firstToken)->not->toBeEmpty();
    
    // 2. Visit another page
    $page->navigate('/blogs');
    
    // 3. Visit test route again to see if it's the same
    $page->navigate('/test-client-token');
    $json = json_decode($page->page()->locator('body')->innerText(), true);
    $secondToken = $json['token'];
    
    expect($secondToken)->toBe($firstToken);
    
    $page->page()->close();
})->group('browser');

it('maintains stable instance_ids across page refreshes', function () {
    // 1. Visit demo page with explicit data_source to avoid redirects
    $page = $this->visit('/mle-demo?theme=bootstrap-5&data_source=demo_default');
    
    // 2. Get instance_id of a specific component from its config
    $selector = '#config-alien-single-permanent';
    $page->assertPresent($selector);
    
    $configJson = $page->page()->locator($selector)->getAttribute('value');
    $config = json_decode($configJson, true);
    $firstInstanceId = $config['instanceId'];
    
    expect($firstInstanceId)->not->toBeEmpty();
    
    // 3. Refresh page
    $page->refresh();
    
    // 4. Get instance_id again
    $page->assertPresent($selector);
    $configJson = $page->page()->locator($selector)->getAttribute('value');
    $config = json_decode($configJson, true);
    $secondInstanceId = $config['instanceId'];
    
    expect($secondInstanceId)->toBe($firstInstanceId);
    
    $page->page()->close();
})->group('browser');

it('assigns unique instance_ids to different components on the same page', function () {
    // 1. Visit demo page
    $page = $this->visit('/mle-demo?theme=bootstrap-5&data_source=demo_default');
    
    // 2. Get instance_ids of two different components
    $sel1 = '#config-alien-single-permanent';
    $sel2 = '#config-alien-multiple-permanent';
    
    $page->assertPresent($sel1)
        ->assertPresent($sel2);
    
    $c1 = json_decode($page->page()->locator($sel1)->getAttribute('value'), true);
    $c2 = json_decode($page->page()->locator($sel2)->getAttribute('value'), true);
    
    $id1 = $c1['instanceId'];
    $id2 = $c2['instanceId'];
    
    expect($id1)->not->toBeEmpty();
    expect($id2)->not->toBeEmpty();
    expect($id1)->not->toBe($id2);
    
    $page->page()->close();
})->group('browser');
