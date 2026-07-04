<?php

declare(strict_types=1);

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature\Components;

use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;
use Illuminate\Support\Facades\Blade;

class MediaManagerCountTest extends TestCase
{
    public function test_exposes_total_media_count_for_single_manager(): void
    {
        $this->withoutVite();

        // Create a model with 1 image in the target collection via helper
        $model = $this->getModelWithMedia(['image' => 1]);

        // Render a single media manager for the images collection
        $html = Blade::render(
            '<x-mle-media-manager
                :id="\'mgr\'"
                :model-or-class-name="$modelOrClassName"
                :collections="[\'image\' => \'image_collection\']"
                :multiple="false"
            />',
            [
                'modelOrClassName' => $model,
            ]
        );

        // Assert the config JSON is present with expected counters
        $this->assertStringContainsString('"totalMediaCount":1', $html);
        $this->assertStringContainsString('"maxMediaCount":1', $html);
        $this->assertStringContainsString('"isAtMax":true', $html);
        $this->assertStringContainsString('"multiple":false', $html);

        // And the visible counter text in the upload form section
        $this->assertStringContainsString('<span class="mle-media-manager-media-counts">1 / 1</span>', $html);
    }
}
