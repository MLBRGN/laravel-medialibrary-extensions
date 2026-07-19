<?php

declare(strict_types=1);

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature\Actions;

use Illuminate\Http\UploadedFile;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultiplePermanentAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;
use Mlbrgn\MediaLibraryExtensions\Tests\TestSupport\Models\Post;

class StoreMultiplePermanentActionCapTest extends TestCase
{
    protected function makeImage(string $name): UploadedFile
    {
        // 1x1 png
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMB/aywF34AAAAASUVORK5CYII=');
        $path = sys_get_temp_dir().'/'.$name;
        file_put_contents($path, $png);

        return new UploadedFile($path, $name, 'image/png', null, true);
    }

    public function test_it_caps_multiple_permanent_uploads_to_remaining_slots(): void
    {
        config()->set('medialibrary-extensions.max_items_in_shared_media_collections', 10);

        $post = Post::factory()->create();

        // Seed the post with 8 existing media items in target collection
        for ($i = 0; $i < 8; $i++) {
            $post->addMedia($this->makeImage('seed-'.$i.'.png'))
                ->toMediaCollection('blog-images-extra');
        }

        $action = app(StoreMultiplePermanentAction::class);

        // Prepare 5 new files
        $files = [
            $this->makeImage('b1.png'),
            $this->makeImage('b2.png'),
            $this->makeImage('b3.png'),
            $this->makeImage('b4.png'),
            $this->makeImage('b5.png'),
        ];

        $symfony = StoreMultipleRequest::create('/mle/perm', 'POST', [
            'temporary_upload_mode' => 'false',
            'collections' => ['image' => 'blog-images-extra'],
            'base_id' => 'mmm',
            'model_type' => $post->getMorphClass(),
            'model_id' => $post->getKey(),
            'data_source' => 'default',
        ], [], ['media' => $files], [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        ]);
        $request = StoreMultipleRequest::createFromBase($symfony);

        $response = $action->execute($request);

        $this->assertTrue(method_exists($response, 'getData')); // JsonResponse
        $data = $response->getData(true);
        $this->assertIsArray($data);
        $this->assertStringContainsString('upload_success', $data['message'] ?? '');
        $this->assertStringContainsString('some uploads failed', $data['message'] ?? '');

        // The post should end up with 10 media in the collection
        $this->assertSame(10, $post->fresh()->getMedia('blog-images-extra')->count());
    }
}
