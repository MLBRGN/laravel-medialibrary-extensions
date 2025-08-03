<?php

use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('test only', function () {
    $media = $this->getTestModel()
        ->addMedia($this->getTestFile('test.jpg'))
        ->preservingOriginal()
        ->toMediaCollection('blog-images');

//    dd(Media::find($media->id));
    $this->assertFileExists($this->getMediaDirectory($media->id.'/test.jpg'));
})->skip();
