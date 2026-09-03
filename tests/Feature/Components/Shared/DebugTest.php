
<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Tests\Support\classes\FakeBladeIconComponent;
use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\Debug;

// Helper to fake Blade UI Kit icon being registered
function fakeBladeIconAlias(string $alias): void
{
    Blade::component(FakeBladeIconComponent::class, $alias);
}

it('renders the debug view with model', function () {
    Config::set('medialibrary-extensions.debug', true);
    $model = $this->getModelWithMedia();
    $component = new Debug(modelReference: $model);
    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class)
        ->and($view->getName())->toBe('medialibrary-extensions::components.shared.debug');
});

it('renders the debug view with model class name', function () {
    Config::set('medialibrary-extensions.debug', true);
    $model = $this->getModelWithMedia();
    $component = new Debug(modelReference: $model->getMorphClass());
    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class)
        ->and($view->getName())->toBe('medialibrary-extensions::components.shared.debug');
});

it('throws when given invalid class name', function () {
    Config::set('medialibrary-extensions.debug', true);
    $this->expectException(\Mlbrgn\MediaLibraryExtensions\Exceptions\InvalidModelTypeException::class);
    $component = new Debug(modelReference: 'fakeClass');
    $view = $component->render();
});

it('throws when given model that does not extend HasMedia interface', function () {
    Config::set('medialibrary-extensions.debug', true);
    //    $this->expectException(TypeError::class);
    // TOODO what exception to expect?
    $this->expectException(\Mlbrgn\MediaLibraryExtensions\Exceptions\InvalidModelTypeException::class);
    $model = $this->getTestModelNotExtendingHasMedia();
    $component = new Debug(modelReference: $model);
    $view = $component->render();
});

it('detects when the Blade UI icon alias exists', function () {
    Config::set('medialibrary-extensions.debug', true);
    $model = $this->getModelWithMedia();

    config(['medialibrary-extensions.icons.delete' => 'trash']);

    fakeBladeIconAlias('trash');

    $component = new Debug(modelReference: $model);

    expect($component->iconExists)->toBeTrue()
        ->and($component->errors)->toBeEmpty();
});

it('detects when the Blade UI icon alias is missing', function () {
    Config::set('medialibrary-extensions.debug', true);
    $model = $this->getModelWithMedia();

    config(['medialibrary-extensions.icons.delete' => 'missing-icon']);

    $component = new Debug(modelReference: $model);

    expect($component->iconExists)->toBeFalse()
        ->and($component->errors)->toHaveCount(1)
        ->and($component->errors[0])->toContain('Blade UI Kit icon package');
});

it('populates media collections from a model', function () {
    Config::set('medialibrary-extensions.debug', true);
    $model = $this->getTestBlogModel();
    $testImage = $this->getFixtureUploadedFile('test.png');
    $model->addMedia($testImage)
        ->toMediaCollection('test-collection');

    $component = new Debug(modelReference: $model);

    expect($component->collections)->toContain('test-collection')
        ->and($component->collections)->toHaveCount(1);
});

it('handles temporary upload (null model) gracefully', function () {
    Config::set('medialibrary-extensions.debug', true);
    $this->expectException(TypeError::class);
    $component = new Debug(modelReference: null);

    expect($component->collections)->toBeInstanceOf(Collection::class)
        ->and($component->collections)->toBeEmpty();
});
