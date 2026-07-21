<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature;

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Rules\AllowedMediaCollections;
use Mlbrgn\MediaLibraryExtensions\Tests\Feature\Support\CollectionRestrictedBlog;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

it('accepts allowed collections', function () {
    $model = new CollectionRestrictedBlog;

    $validator = Validator::make(
        [
            'collections' => ['allowed-collection'],
        ],
        [
            'collections' => [
                new AllowedMediaCollections($model),
            ],
        ]
    );

    expect($validator->passes())->toBeTrue();
});

it('accepts multiple allowed collections', function () {
    $model = new CollectionRestrictedBlog;

    $validator = Validator::make(
        [
            'collections' => [
                'allowed-collection',
                'another-allowed-collection',
            ],
        ],
        [
            'collections' => [
                new AllowedMediaCollections($model),
            ],
        ]
    );

    expect($validator->passes())->toBeTrue();
});

it('accepts when the model allows all collections', function () {
    $model = new Blog;

    $validator = Validator::make(
        [
            'collections' => ['anything'],
        ],
        [
            'collections' => [
                new AllowedMediaCollections($model),
            ],
        ]
    );

    expect($validator->passes())->toBeTrue();
});

it('rejects a disallowed collection', function () {
    $model = new CollectionRestrictedBlog;

    $validator = Validator::make(
        [
            'collections' => ['forbidden-collection'],
        ],
        [
            'collections' => [
                new AllowedMediaCollections($model),
            ],
        ]
    );

    expect($validator->fails())->toBeTrue();

    expect($validator->errors()->first('collections'))
        ->toBe(__('medialibrary-extensions::messages.selected_media_collection_not_allowed'));
});

it('rejects when any collection is not allowed', function () {
    $model = new CollectionRestrictedBlog;

    $validator = Validator::make(
        [
            'collections' => [
                'allowed-collection',
                'forbidden-collection',
            ],
        ],
        [
            'collections' => [
                new AllowedMediaCollections($model),
            ],
        ]
    );

    expect($validator->fails())->toBeTrue();

    expect($validator->errors()->first('collections'))
        ->toBe(__('medialibrary-extensions::messages.selected_media_collection_not_allowed'));
});

it('accepts duplicate collections', function () {
    $model = new CollectionRestrictedBlog;

    $validator = Validator::make(
        [
            'collections' => [
                'allowed-collection',
                'allowed-collection',
            ],
        ],
        [
            'collections' => [
                new AllowedMediaCollections($model),
            ],
        ]
    );

    expect($validator->passes())->toBeTrue();
});

it('accepts an empty allowed collection list as unrestricted', function () {
    $model = new Blog;

    $validator = Validator::make(
        [
            'collections' => [
                'anything',
                'else',
            ],
        ],
        [
            'collections' => [
                new AllowedMediaCollections($model),
            ],
        ]
    );

    expect($validator->passes())->toBeTrue();
});
