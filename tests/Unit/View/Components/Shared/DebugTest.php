
<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\Tests\Support\classes\FakeBladeIconComponent;
use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\Debug;

// Helper to fake Blade UI Kit icon being registered
function fakeBladeIconAlias(string $alias): void
{
    Blade::component(FakeBladeIconComponent::class, $alias);
}

it('renders the debug view with model', function () {
    $model = $this->getModelWithMedia();
    $component = new Debug(modelOrClassName: $model);
    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class)
        ->and($view->getName())->toBe('media-library-extensions::components.shared.debug');
});

it('renders the debug view with model class name', function () {
    $model = $this->getModelWithMedia();
    $component = new Debug(modelOrClassName: $model->getMorphClass());
    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class)
        ->and($view->getName())->toBe('media-library-extensions::components.shared.debug');
});

it('throws when given invalid class name', function () {
    $this->expectException(InvalidArgumentException::class);
    $model = $this->getModelWithMedia();
    $component = new Debug(modelOrClassName: 'fakeClass');
    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class)
        ->and($view->getName())->toBe('media-library-extensions::components.shared.debug');
});

it('throws when given model that does not extend HasMedia interface', function () {
    $this->expectException(TypeError::class);
    $model = $this->getTestModelNotExtendingHasMedia();
    $component = new Debug(modelOrClassName: $model);
    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class)
        ->and($view->getName())->toBe('media-library-extensions::components.shared.debug');
});

it('detects when the Blade UI icon alias exists', function () {
    $model = $this->getModelWithMedia();

    config(['media-library-extensions.icons.delete' => 'trash']);

    fakeBladeIconAlias('trash');

    $component = new Debug(modelOrClassName: $model);

    expect($component->iconExists)->toBeTrue()
        ->and($component->errors)->toBeEmpty();
});

it('detects when the Blade UI icon alias is missing', function () {
    $model = $this->getModelWithMedia();

    config(['media-library-extensions.icons.delete' => 'missing-icon']);

    $component = new Debug(modelOrClassName: $model);

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
    $this->expectException(TypeError::class);
    $component = new Debug(modelOrClassName: null);

    expect($component->collections)->toBeInstanceOf(Collection::class)
        ->and($component->collections)->toBeEmpty();
});
