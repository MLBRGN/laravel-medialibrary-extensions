<?php


use Mlbrgn\MediaLibraryExtensions\Support\DebugManager;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManager;
use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\Debug;

beforeEach(function () {
    config(['medialibrary-extensions.debug' => true]);
    DebugManager::reset();
});

it('registers components under the correct scope in media manager', function () {
    $model = $this->getTestBlogModel();

    $component = new MediaManager(
        id: 'test-mm',
        modelOrClassName: $model,
        collections: ['image' => 'images'],
        multiple: true
    );

    // Actual ID should be 'test-mm'
    $actualId = 'test-mm';

    // 1. Check registration during constructor (no scope pushed yet)
    $globalComponents = DebugManager::getRegisteredComponents('global');

    expect($globalComponents)
        ->toHaveKey($actualId);

    expect($globalComponents[$actualId]['name'])
        ->toBe('MediaManager');

    // 2. Simulate MediaManager::render()
    $component->render();

    DebugManager::pushScope($actualId);
    DebugManager::register('sub-comp-1', 'SubComp', [], []);

    expect(DebugManager::getRegisteredComponents($actualId))
        ->toHaveKey('sub-comp-1');

    DebugManager::popScope();
});

it('retrieves components for a specific scope in debug component', function () {
    $model = $this->getTestBlogModel();
    $actualId = 'my-mm-mmm';

    DebugManager::pushScope($actualId);

    DebugManager::register('sub-1', 'SubComp', [], []);

    $debugComp = new Debug(
        modelOrClassName: $model,
        config: ['id' => $actualId]
    );

    $registered = $debugComp->getComponents();

    expect($registered)
        ->toHaveKey('sub-1');

    expect($registered)
        ->toHaveCount(1);

    DebugManager::popScope();
});

it('reproduces the zero registered components issue if ids do not match', function () {
    $model = $this->getTestBlogModel();

    DebugManager::pushScope('parent-id');
    DebugManager::register('child-id', 'ChildComp', [], []);
    DebugManager::popScope();

    $debugComp = new Debug(
        modelOrClassName: $model,
        config: ['id' => 'different-id']
    );

    expect($debugComp->getComponents())
        ->toBeEmpty();
});
//
//namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature;
//
//use Mlbrgn\MediaLibraryExtensions\Support\DebugManager;
//use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;
//use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManager;
//use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\Debug;
//
//require_once __DIR__.'/../TestCase.php';
//
//class DebugManagerScopingTest extends TestCase
//{
//    protected function setUp(): void
//    {
//        parent::setUp();
//        config(['medialibrary-extensions.debug' => true]);
//        DebugManager::reset();
//    }
//
//    public function test_it_registers_components_under_the_correct_scope_in_media_manager()
//    {
//        $model = $this->getTestBlogModel();
//
//        $component = new MediaManager(
//            id: 'test-mm',
//            modelOrClassName: $model,
//            collections: ['image' => 'images'],
//            multiple: true
//        );
//
//        // Actual ID should be 'test-mm-mmm' because multiple is true
//        $actualId = 'test-mm-mmm';
//
//        // 1. Check registration during constructor (no scope pushed yet)
//        $globalComponents = DebugManager::getRegisteredComponents('global');
////        dd($globalComponents);
//        $this->assertArrayHasKey($actualId, $globalComponents);
//        $this->assertEquals('MediaManager', $globalComponents[$actualId]['name']);
//
//        // 2. Simulate MediaManager::render()
//        $view = $component->render();
//
//        // Inside render, pushScope happens, then getView, then popScope.
//        // We can't easily test what happened INSIDE render() without mocking or manual scope management
//        // But we can test manual scope management here.
//        DebugManager::pushScope($actualId);
//        DebugManager::register('sub-comp-1', 'SubComp', [], []);
//
//        $this->assertArrayHasKey('sub-comp-1', DebugManager::getRegisteredComponents($actualId));
//
//        DebugManager::popScope();
//    }
//
//    public function test_it_correctly_retrieves_components_for_a_specific_scope_in_debug_component()
//    {
//        $model = $this->getTestBlogModel();
//        $actualId = 'my-mm-mmm';
//
//        // Simulate MediaManager rendering process
//        DebugManager::pushScope($actualId);
//
//        // Sub-component registers itself
//        DebugManager::register('sub-1', 'SubComp', [], []);
//
//        // Debug component is instantiated
//        $debugComp = new Debug(modelOrClassName: $model, config: ['id' => $actualId]);
//
//        // Check if Debug component sees the sub-component
//        $registered = $debugComp->getComponents();
//
//        $this->assertArrayHasKey('sub-1', $registered);
//        $this->assertCount(1, $registered);
//
//        DebugManager::popScope();
//    }
//
//    public function test_reproduces_the_zero_registered_components_issue_if_ids_do_not_match()
//    {
//        $model = $this->getTestBlogModel();
//
//        DebugManager::pushScope('parent-id');
//        DebugManager::register('child-id', 'ChildComp', [], []);
//        DebugManager::popScope();
//
//        // Debug component looking for 'different-id'
//        $debugComp = new Debug(modelOrClassName: $model, config: ['id' => 'different-id']);
//
//        $this->assertEmpty($debugComp->getComponents());
//    }
//}
