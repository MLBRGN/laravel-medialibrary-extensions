<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxMediaCount;

beforeEach(function () {
    $this->model = mock(HasMediaExtended::class);
});

it('passes when adding fewer than or equal to the allowed max', function () {
    $this->model
        ->shouldReceive('getMedia')
        ->with('images')
        ->andReturn(collect());

    $validator = Validator::make(
        ['media' => ['file1', 'file2']],
        ['media' => [new MaxMediaCount(
            $this->model,
            ['image' => 'images'],
            3,
        )]],
    );

    expect($validator->passes())->toBeTrue();
});

it('fails when adding more than the allowed max', function () {
    $this->model
        ->shouldReceive('getMedia')
        ->with('videos')
        ->andReturn(collect(['existing1']));

    $rule = new MaxMediaCount(
        $this->model,
        ['video' => 'videos'],
        3,
    );

    $validator = Validator::make(
        ['media' => ['file1', 'file2', 'file3']],
        ['media' => [$rule]],
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('media'))
        ->toBe($rule->message());
});

it('does not fail when a non-array value makes the total equal to max', function () {
    $this->model
        ->shouldReceive('getMedia')
        ->with('audio')
        ->andReturn(collect(['existing1', 'existing2']));

    $validator = Validator::make(
        ['media' => 'singleFile'],
        ['media' => [new MaxMediaCount(
            $this->model,
            ['audio' => 'audio'],
            3,
        )]],
    );

    expect($validator->passes())->toBeTrue();
});

it('fails when a non-array value causes the total to exceed max', function () {
    $this->model
        ->shouldReceive('getMedia')
        ->with('documents')
        ->andReturn(collect(['existing1', 'existing2']));

    $rule = new MaxMediaCount(
        $this->model,
        ['document' => 'documents'],
        2,
    );

    $validator = Validator::make(
        ['media' => 'singleFile'],
        ['media' => [$rule]],
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('media'))
        ->toBe($rule->message());
});

it('returns the singular message when max is one', function () {
    $rule = new MaxMediaCount(
        $this->model,
        ['image' => 'images'],
        1,
    );

    expect($rule->message())
        ->toBe(__('medialibrary-extensions::messages.only_one_medium_allowed'));
});

it('returns the plural message when max is greater than one', function () {
    $rule = new MaxMediaCount(
        $this->model,
        ['image' => 'images'],
        5,
    );

    expect($rule->message())
        ->toBe(
            __('medialibrary-extensions::messages.this_collection_can_contain_up_to_:items_items', [
                'items' => 5,
            ])
        );
});
