<?php

it('test only', function () {
    $testImage = $this->getUploadedFile('test.jpg');
    $media = $this->getTestBlogModel()
        ->addMedia($testImage)
        ->toMediaCollection('blog-images');

//    dd(Media::find($media->id));
    $this->assertFileExists($this->getMediaDirectory($media->id.'/test.jpg'));
});
