<?php

use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

it('renders the single media manager component', function () {
    $blog = $this->getTestBlogModel();
    $output = Blade::render('<x-mle-media-manager-single
        :model-or-class-name="$model"
        :collections="$collections"
         />', [
        'model' => $blog,
        'collections' => ['image' => 'blog-main'],
    ]);

    expect($output)->toContain('<div class="media-manager');
});

it('renders the multiple media manager component', function () {
    $blog = $this->getTestBlogModel();
    $output = Blade::render('<x-mle-media-manager-multiple
        :model-or-class-name="$model"
        :collections="$collections"
         />', [
        'model' => $blog,
        'collections' => ['image' => 'blog-main'],
    ]);

    expect($output)->toContain('<div class="media-manager');
});
