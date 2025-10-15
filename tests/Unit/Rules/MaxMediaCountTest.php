<?php

use Mlbrgn\MediaLibraryExtensions\Rules\MaxMediaCount;
use Spatie\MediaLibrary\HasMedia;

beforeEach(function () {
    $this->model = mock(HasMedia::class);
});

it('passes when adding fewer than or equal to the allowed max', function () {
    $this->model
        ->shouldReceive('getMedia')
        ->with('images')
        ->andReturn(collect([])); // no existing media

    $rule = new MaxMediaCount($this->model, 'images', 3);

    $failed = false;

    $rule->validate('media', ['file1', 'file2'], function () use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeFalse();
});

it('fails when adding more than the allowed max', function () {
    $this->model
        ->shouldReceive('getMedia')
        ->with('images')
        ->andReturn(collect(['existing1'])); // 1 existing media

    $rule = new MaxMediaCount($this->model, 'images', 3);

    $failed = false;

    $rule->validate('media', ['file1', 'file2', 'file3'], function () use (&$failed, $rule) {
        $failed = true;
        expect(func_get_arg(0))->toBe($rule->message());
    });

    expect($failed)->toBeTrue();
});

it('does not fail when non-array value makes total equal to max', function () {
    // 2 existing items
    $this->model
        ->shouldReceive('getMedia')
        ->with('images')
        ->andReturn(collect(['existing1', 'existing2']));

    // max = 3, newCount (non-array) = 1 -> total = 3 -> should NOT fail
    $rule = new MaxMediaCount($this->model, 'images', 3);

    $failed = false;

    $rule->validate('media', 'singleFile', function ($message) use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeFalse();
});

it('fails when non-array value causes total to exceed max and returns message', function () {
    // 2 existing items
    $this->model
        ->shouldReceive('getMedia')
        ->with('images')
        ->andReturn(collect(['existing1', 'existing2']));

    // Set max = 2 so existing(2) + new(1) = 3 > 2 -> should fail
    $rule = new MaxMediaCount($this->model, 'images', 2);

    $receivedMessage = null;

    $rule->validate('media', 'singleFile', function ($message) use (&$receivedMessage) {
        $receivedMessage = $message;
    });

    expect($receivedMessage)->toBe($rule->message());
});

it('returns the correct message', function () {
    $rule = new MaxMediaCount($this->model, 'images', 5);

    expect($rule->message())
        ->toBe(__('media-library-extensions::messages.this_collection_can_contain_up_to_:items_items', ['items' => 5]));
});
