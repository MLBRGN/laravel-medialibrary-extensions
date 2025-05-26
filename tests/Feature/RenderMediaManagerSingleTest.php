<?php

use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

it('renders the single media manager component', function () {
    $blog = Blog::factory()->create();
    $output = Blade::render('<x-mle-media-manager-single :model="$model" media-collection-name="blog-main" />', [
        'model' => $blog,
    ]);

    expect($output)->toContain('<div class="media-manager');
})->skip();
