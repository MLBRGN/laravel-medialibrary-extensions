<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Database\Factories\TemporaryUploadFactory;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxTemporaryUploadCount;

it('passes when adding fewer than or equal to the allowed max', function () {
    $clientToken = 'test-client';

    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->create();

    $validator = Validator::make(
        ['media' => ['file1']],
        ['media' => [new MaxTemporaryUploadCount(
            ['images'],
            2,
            null,
            'default',
            $clientToken,
        )]],
    );

    expect($validator->passes())->toBeTrue();
});

it('fails when adding more than the allowed max', function () {
    $clientToken = 'test-client';

    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->create();

    $rule = new MaxTemporaryUploadCount(
        ['images'],
        1,
        null,
        'default',
        $clientToken,
    );

    $validator = Validator::make(
        ['media' => ['file1']],
        ['media' => [$rule]],
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('media'))
        ->toBe($rule->message());
});

it('does not fail when a non-array value makes the total equal to max', function () {
    $clientToken = 'test-client';

    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->create();

    $rule = new MaxTemporaryUploadCount(
        ['images'],
        2,
        null,
        'default',
        $clientToken,
    );

    $validator = Validator::make(
        ['media' => 'file1'],
        ['media' => [$rule]],
    );

    expect($validator->passes())->toBeTrue();
});

it('fails when a non-array value causes the total to exceed max', function () {
    $clientToken = 'test-client';

    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->create();

    $rule = new MaxTemporaryUploadCount(
        ['images'],
        1,
        null,
        'default',
        $clientToken,
    );

    $validator = Validator::make(
        ['media' => 'file1'],
        ['media' => [$rule]],
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('media'))
        ->toBe($rule->message());
});

it('only counts temporary uploads in the requested collections', function () {
    $clientToken = 'test-client';

    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->create();

    TemporaryUploadFactory::new()
        ->forCollection('documents')
        ->forClient($clientToken)
        ->create();

    $validator = Validator::make(
        ['media' => ['file1']],
        ['media' => [new MaxTemporaryUploadCount(
            ['images'],
            2,
            null,
            'default',
            $clientToken,
        )]],
    );

    expect($validator->passes())->toBeTrue();
});

it('only counts temporary uploads for the specified instance', function () {
    $clientToken = 'test-client';

    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->state([
            'instance_id' => 'instance-1',
        ])
        ->create();

    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->state([
            'instance_id' => 'instance-2',
        ])
        ->create();

    $rule = new MaxTemporaryUploadCount(
        ['images'],
        2,
        'instance-1',
        'default',
        $clientToken,
    );

    $validator = Validator::make(
        ['media' => ['file1']],
        ['media' => [$rule]],
    );

    expect($validator->passes())->toBeTrue();
});

it('only counts temporary uploads for the specified client token', function () {
    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient('client-1')
        ->create();

    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient('client-2')
        ->create();

    $rule = new MaxTemporaryUploadCount(
        ['images'],
        2,
        null,
        'default',
        'client-1',
    );

    $validator = Validator::make(
        ['media' => ['file1']],
        ['media' => [$rule]],
    );

    expect($validator->passes())->toBeTrue();
});

it('returns the singular message when max is one', function () {
    $rule = new MaxTemporaryUploadCount(
        ['images'],
        1,
    );

    expect($rule->message())
        ->toBe(__('medialibrary-extensions::messages.only_one_medium_allowed'));
});

it('returns the plural message when max is greater than one', function () {
    $rule = new MaxTemporaryUploadCount(
        ['images'],
        5,
    );

    expect($rule->message())
        ->toBe(
            __('medialibrary-extensions::messages.this_collection_can_contain_up_to_:items_items', [
                'items' => 5,
            ])
        );
});
