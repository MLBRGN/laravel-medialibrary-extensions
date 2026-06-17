<?php

it('test only', function () {
    $testImage = $this->getFixtureUploadedFile('test.jpg');
    $media = $this->getTestBlogModel()
        ->addMedia($testImage)
        ->toMediaCollection('blog-images');

    $this->assertFileExists($this->getMediaDirectory($media->id.'/test.jpg'));
});
