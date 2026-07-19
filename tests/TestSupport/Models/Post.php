<?php

declare(strict_types=1);

namespace Mlbrgn\MediaLibraryExtensions\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Mlbrgn\MediaLibraryExtensions\Tests\Support\Models\TestPost as BaseTestPost;

class Post extends BaseTestPost
{
    /**
     * Reuse the existing blogs table for simplicity in tests.
     * This table is already migrated in the package TestCase.
     */
    protected $table = 'blogs';

    /**
     * Minimal inline factory to support Post::factory()->create().
     */
    public static function factory(array $attributes = []): EloquentFactory
    {
        return new class($attributes) extends EloquentFactory
        {
            protected $model = Post::class;

            public function __construct(protected array $attrs = [])
            {
                parent::__construct();
            }

            public function definition(): array
            {
                return array_merge([
                    'title' => fake()->sentence(),
                ], $this->attrs);
            }
        };
    }
}
