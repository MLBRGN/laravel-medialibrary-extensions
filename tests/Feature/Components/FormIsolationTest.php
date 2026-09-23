<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Database\Factories\TemporaryUploadFactory;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxMediaCount;
use Mlbrgn\MediaLibraryExtensions\Rules\MinMediaCount;
use Mlbrgn\MediaLibraryExtensions\Tests\Support\classes\ExtendedBaseComponent;

it('includes isolationFormId in render data', function () {
    $component = new ExtendedBaseComponent('my-component-id');
    // Using a partial that we know exists in the plain theme
    $view = $component->renderView('upload-form', theme: 'plain', isPartial: true);
    
    expect($view->getData()['isolationFormId'])->toBe('mle-isolated-my-component-id');
});

it('renders form attribute and mle_instance_map in media-manager', function () {
    $id = 'my-manager';
    $name = 'gallery';
    
    $html = \Illuminate\Support\Facades\Blade::render(
        '<x-mle-media-manager :id="$id" :model-reference="$model" :name="$name" :collections="$collections" />',
        [
            'id' => $id,
            'model' => \Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog::class,
            'name' => $name,
            'collections' => ['blog-gallery' => 'blog-gallery']
        ]
    );

    expect($html)->toContain('form="mle-isolated-'.$id.'"')
        ->toContain('name="mle_instance_map['.$name.']"')
        ->toContain('name="'.$name.'"'); // The media count input
});

it('renders form attribute in upload-form partial', function () {
    $id = 'my-manager';
    
    $html = \Illuminate\Support\Facades\Blade::render(
        '<x-mle-partial-upload-form :id="$id" :model-reference="$model" :collections="$collections" />',
        [
            'id' => $id,
            'model' => \Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog::class,
            'collections' => ['blog-gallery' => 'blog-gallery']
        ]
    );

    expect($html)->toContain('form="mle-isolated-'.$id.'"');
});

it('renders form attribute and mle_instance_map in media-lab', function () {
    $id = 'my-lab';
    $name = 'lab-field';
    
    // Create a real medium for the lab
    $media = $this->getMedium();
    
    $html = \Illuminate\Support\Facades\Blade::render(
        '<x-mle-media-lab :id="$id" :name="$name" :media="$media" />',
        [
            'id' => $id,
            'name' => $name,
            'media' => $media,
        ]
    );

    expect($html)->toContain('form="mle-isolated-'.$id.'"')
        ->toContain('name="mle_instance_map['.$name.']"');
});

it('MinMediaCount rule resolves instanceId from mle_instance_map', function () {
    $clientToken = 'test-client';
    $instanceId = 'instance-123';
    $otherInstanceId = 'instance-456';
    $attributeName = 'gallery';

    request()->merge([
        'client_token' => $clientToken,
        // The attribute-specific map must take precedence over this fallback.
        'instance_id' => $otherInstanceId,
        'mle_instance_map' => [
            $attributeName => $instanceId,
        ],
    ]);

    // Create 2 uploads for the target instance
    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->state(['instance_id' => $instanceId])
        ->count(2)
        ->create();

    // This upload must be ignored because it belongs to another instance.
    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->state(['instance_id' => $otherInstanceId])
        ->create();

    $rule = new MinMediaCount(null, ['images'], 2);
    $validator = Validator::make([$attributeName => 2], [$attributeName => [$rule]]);

    expect($validator->passes())->toBeTrue();

    // The mapped instance has only two uploads; the other instance must not be counted.
    $rule3 = new MinMediaCount(null, ['images'], 3);
    $validator3 = Validator::make([$attributeName => 2], [$attributeName => [$rule3]]);
    expect($validator3->fails())->toBeTrue();
});

it('MaxMediaCount rule resolves instanceId from mle_instance_map', function () {
    $clientToken = 'test-client';
    $instanceId = 'instance-123';
    $otherInstanceId = 'instance-456';
    $attributeName = 'gallery';

    request()->merge([
        'client_token' => $clientToken,
        // The attribute-specific map must take precedence over this fallback.
        'instance_id' => $otherInstanceId,
        'mle_instance_map' => [
            $attributeName => $instanceId,
        ],
    ]);

    // Create 3 uploads for the target instance
    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->state(['instance_id' => $instanceId])
        ->count(3)
        ->create();

    // This upload must be ignored because it belongs to another instance.
    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->state(['instance_id' => $otherInstanceId])
        ->create();

    $rule = new MaxMediaCount(null, ['images'], 3);
    $validator = Validator::make([$attributeName => 3], [$attributeName => [$rule]]);

    expect($validator->passes())->toBeTrue();

    // The mapped instance has three uploads; the other instance must not be counted.
    $rule2 = new MaxMediaCount(null, ['images'], 2);
    $validator2 = Validator::make([$attributeName => 3], [$attributeName => [$rule2]]);
    expect($validator2->fails())->toBeTrue();
});
