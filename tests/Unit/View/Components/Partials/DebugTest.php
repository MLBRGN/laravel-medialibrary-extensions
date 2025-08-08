<?php

use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Debug;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use function Pest\Laravel\get;

// A fake HasMedia model for testing
//class FakeHasMediaModel extends Model implements HasMedia
//{
//    use \Spatie\MediaLibrary\InteractsWithMedia;
//
//    protected $table = 'fake_models'; // to avoid migration issues
//}

// Helper to fake Blade UI Kit icon being registered
function fakeBladeIconAlias(string $alias): void {
    Blade::component($alias, fn () => '');
}

it('renders the debug view', function () {
    $component = new Debug();
    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class)
        ->and($view->getName())->toBe('media-library-extensions::components.partial.debug');
});

it('detects when the Blade UI icon alias exists', function () {
    // Fake alias expected from config
    config(['media-library-extensions.icons.delete' => 'trash']);

    fakeBladeIconAlias('trash');

    $component = new Debug();
//    expect($component->iconExists)->toBeTrue();
//        ->and($component->errors)->toBeEmpty();
})->skip();

it('detects when the Blade UI icon alias is missing', function () {
    config(['media-library-extensions.icons.delete' => 'missing-icon']);

    $component = new Debug();

    expect($component->iconExists)->toBeFalse()
        ->and($component->errors)->toHaveCount(1)
        ->and($component->errors[0])->toContain('Blade UI Kit icon package');
});

it('populates media collections from model', function () {
    $model = $this->getTestBlogModel();
    $testImage = $this->getUploadedFile('test.png');
    $model->addMedia($testImage)
        ->toMediaCollection('test-collection');

    $component = new Debug(model: $model);

    expect($component->collections)->toContain('test-collection')
        ->and($component->collections)->toHaveCount(1);
});

it('handles null model gracefully', function () {
    $component = new Debug();

    expect($component->collections)->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($component->collections)->toBeEmpty();
});
