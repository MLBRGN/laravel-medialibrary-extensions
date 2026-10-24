<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature\View;

use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaCarousel;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaModal;

class CarouselUnscopedTemporaryUploadsTest extends TestCase
{
    public function test_carousel_with_null_instance_id_shows_all_temporary_uploads()
    {
        $alien = Alien::create([]);

        config(['medialibrary-extensions.test_client_token' => 'TEST_TOKEN']);

        // Create a temporary upload with a specific instance ID
        $this->createTemporaryUpload([
            'collection_name' => 'alien-multiple-images',
            'instance_id' => 'SOME_OTHER_ID',
            'client_token' => 'TEST_TOKEN',
        ]);

        // Create carousel with instanceId = null
        $carousel = new MediaCarousel(
            id: 'alien-carousel',
            modelReference: $alien,
            collections: ['image' => 'alien-multiple-images'],
            options: ['includeTemporaryUploads' => true],
            instanceId: null,
            clientToken: 'TEST_TOKEN'
        );

        $this->assertCount(1, $carousel->media);
    }

    public function test_nested_carousel_in_modal_also_shows_all_temporary_uploads_when_parent_is_unscoped()
    {
        $alien = Alien::create([]);

        config(['medialibrary-extensions.test_client_token' => 'TEST_TOKEN']);

        $this->createTemporaryUpload([
            'collection_name' => 'alien-multiple-images',
            'instance_id' => 'SOME_OTHER_ID',
            'client_token' => 'TEST_TOKEN',
        ]);

        // 1. Create the outer carousel
        $outerCarousel = new MediaCarousel(
            id: 'alien-carousel',
            modelReference: $alien,
            collections: ['image' => 'alien-multiple-images'],
            options: ['includeTemporaryUploads' => true],
            instanceId: null,
            clientToken: 'TEST_TOKEN'
        );

        $this->assertCount(1, $outerCarousel->media);

        // 2. Simulate the modal being created (as it is in the Blade template of the outer carousel)
        // In the template, it's called as:
        // <x-mle-media-modal :id="$id" :model-reference="$modelReference" :collections="$collections" :options="$getOptions()" ... />
        $modal = new MediaModal(
            id: $outerCarousel->id,
            modelReference: $outerCarousel->modelReference,
            collections: $outerCarousel->collections,
            title: 'Test Modal',
            options: $outerCarousel->getOptions(),
            instanceId: $outerCarousel->instanceId, // Passed from outer carousel in updated templates
            clientToken: 'TEST_TOKEN'
        );

        // 3. Simulate the inner carousel being created (as it is in media-modal.blade.php)
        // <x-mle-media-carousel :id="$getDomId()" :instance-id="$instanceId" ... />
        // Note: $instanceId here comes from $modal->instanceId
        $innerCarousel = new MediaCarousel(
            id: $modal->getDomId(),
            modelReference: $modal->modelReference,
            collections: $modal->collections,
            options: $modal->getOptions(),
            instanceId: $modal->instanceId, // This is what happens currently in Blade
            clientToken: 'TEST_TOKEN'
        );

        // This is the failure point we expect
        $this->assertCount(1, $innerCarousel->media, 'Inner carousel should show the temporary upload');
    }
}
