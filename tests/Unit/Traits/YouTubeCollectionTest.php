<?php

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;

uses(TestCase::class);

it('adds a YouTube media collection as single file', function () {
    $model = Blog::create(['title' => 'My Blog Title']);

    // Manually trigger the registration
    $model->addYouTubeCollection('my-collection');

    //    dd($model->mediaCollections);
    //    $collection = collect($model->mediaCollections)->firstWhere('name', 'youtube_videos');
    //
    //    expect($collection)->not->toBeNull()
    //        ->and($collection->singleFile)->toBeTrue();
})->todo();
