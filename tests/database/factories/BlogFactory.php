<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
        ];
    }
}
