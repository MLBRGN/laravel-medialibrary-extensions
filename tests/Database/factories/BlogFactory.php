<?php
namespace Mlbrgn\SpatieMediaLibraryExtensions\Tests\Database\Factories;

use Mlbrgn\SpatieMediaLibraryExtensions\Tests\Models\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;

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
