
<?php

use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\Debug;

// Minimal fake component for testing Blade UI Kit icons
class FakeBladeIconComponent extends \Illuminate\View\Component
{
    public function render()
    {
        return '';
    }
}

// Helper to fake Blade UI Kit icon being registered
function fakeBladeIconAlias(string $alias): void
{
    Blade::component(FakeBladeIconComponent::class, $alias);
}

it('renders the debug view', function () {
    $component = new Debug(modelOrClassName: 'FakeModel');
    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class)
        ->and($view->getName())->toBe('media-library-extensions::components.shared.debug');
});

it('detects when the Blade UI icon alias exists', function () {
    config(['media-library-extensions.icons.delete' => 'trash']);

    fakeBladeIconAlias('trash');

    $component = new Debug(modelOrClassName: 'FakeModel');

    expect($component->iconExists)->toBeTrue()
        ->and($component->errors)->toBeEmpty();
});

it('detects when the Blade UI icon alias is missing', function () {
    config(['media-library-extensions.icons.delete' => 'missing-icon']);

    $component = new Debug(modelOrClassName: 'FakeModel');

    expect($component->iconExists)->toBeFalse()
        ->and($component->errors)->toHaveCount(1)
        ->and($component->errors[0])->toContain('Blade UI Kit icon package');
});

it('populates media collections from a model', function () {
    $model = $this->getTestBlogModel();
    $testImage = $this->getFixtureUploadedFile('test.png');
    $model->addMedia($testImage)
        ->toMediaCollection('test-collection');

    $component = new Debug(modelOrClassName: $model);

    expect($component->collections)->toContain('test-collection')
        ->and($component->collections)->toHaveCount(1);
});

it('handles temporary upload (null model) gracefully', function () {
    $component = new Debug(modelOrClassName: 'FakeModel');

    expect($component->collections)->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($component->collections)->toBeEmpty();
});
