<?php

use Illuminate\Support\Facades\Blade;

it('renders the single media manager component', function () {
    $blog = $this->getTestBlogModel();
    $output = Blade::render('<x-mle-media-manager-single
        id="test-id"
        :model-reference="$model"
        :collections="$collections"
         />', [
        'model' => $blog,
        'collections' => ['image' => 'blog-main'],
    ]);

    expect($output)->toContain('mle-media-manager');
});

it('renders the multiple media manager component', function () {
    $blog = $this->getTestBlogModel();
    $output = Blade::render('<x-mle-media-manager-multiple
        id="test-id"
        :model-reference="$model"
        :collections="$collections"
         />', [
        'model' => $blog,
        'collections' => ['image' => 'blog-main'],
    ]);

    expect($output)->toContain('mle-media-manager');
});
