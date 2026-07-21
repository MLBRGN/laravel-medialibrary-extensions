<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreUpdatedMediaRequest;
use Mlbrgn\MediaLibraryExtensions\Tests\Feature\Support\CollectionRestrictedBlog;
use Mlbrgn\MediaLibraryExtensions\Tests\Feature\Support\DeniedDeleteBlog;
use Mlbrgn\MediaLibraryExtensions\Tests\Feature\Support\DeniedEditBlog;
use Mlbrgn\MediaLibraryExtensions\Tests\Feature\Support\DeniedUploadBlog;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\User;

beforeEach(function () {
    Session::start();
});

class TestBlogPolicy
{
    public function uploadMedia(?User $user, Blog $blog): bool
    {
        return $user?->id === 1;
    }

    public function editMedia(?User $user, Blog $blog): bool
    {
        return $user?->id === 1;
    }

    public function deleteMedia(?User $user, Blog $blog): bool
    {
        return $user?->id === 1;
    }
}

it('allows media actions when no policy exists', function () {
    $blog = Blog::create(['title' => 'Test']);

    expect(createStoreRequest($blog)->authorize())->toBeTrue();
    expect(createDestroyRequest($blog)->authorize())->toBeTrue();
    expect(createEditRequest($blog)->authorize())->toBeTrue();
});

it('uses the model policy for upload authorization', function () {
    Gate::policy(Blog::class, TestBlogPolicy::class);

    $blog = Blog::create(['title' => 'Test']);

    expect(createStoreRequest($blog, new User(['id' => 1]))->authorize())
        ->toBeTrue();

    expect(createStoreRequest($blog, new User(['id' => 2]))->authorize())
        ->toBeFalse();
});

it('uses the model policy for delete authorization', function () {
    Gate::policy(Blog::class, TestBlogPolicy::class);

    $blog = Blog::create(['title' => 'Test']);

    expect(createDestroyRequest($blog, new User(['id' => 1]))->authorize())
        ->toBeTrue();

    expect(createDestroyRequest($blog, new User(['id' => 2]))->authorize())
        ->toBeFalse();
});

it('uses the model policy for edit authorization', function () {
    Gate::policy(Blog::class, TestBlogPolicy::class);

    $blog = Blog::create(['title' => 'Test']);

    expect(createEditRequest($blog, new User(['id' => 1]))->authorize())
        ->toBeTrue();

    expect(createEditRequest($blog, new User(['id' => 2]))->authorize())
        ->toBeFalse();
});

it('denies uploads when uploads are disabled on the model', function () {
    $blog = DeniedUploadBlog::create([
        'title' => 'Denied Upload',
    ]);

    expect(createStoreRequest($blog)->authorize())
        ->toBeFalse();
});

it('denies deletes when deletes are disabled on the model', function () {
    $blog = DeniedDeleteBlog::create([
        'title' => 'Denied Delete',
    ]);

    expect(createDestroyRequest($blog)->authorize())
        ->toBeFalse();
});

it('denies edits when edits are disabled on the model', function () {
    $blog = DeniedEditBlog::create([
        'title' => 'Denied Edit',
    ]);

    expect(createEditRequest($blog)->authorize())
        ->toBeFalse();
});

it('restricts media actions by collection', function () {
    $model = CollectionRestrictedBlog::create([
        'title' => 'Collection Restricted',
    ]);

    expect(
        createStoreRequest($model, null, ['allowed-collection'])
            ->authorize()
    )->toBeTrue();

    expect(
        createStoreRequest($model, null, ['forbidden-collection'])
            ->authorize()
    )->toBeFalse();

    expect(
        createStoreRequest(
            $model,
            null,
            ['allowed-collection', 'forbidden-collection']
        )->authorize()
    )->toBeFalse();
})->todo('move this is validation, not authorization');

// Helper functions

function createStoreRequest($model, $user = null, $collections = ['images'])
{
    $request = StoreSingleRequest::create('/upload', 'POST', [
        'model_type' => get_class($model),
        'model_id' => $model->id,
        'collections' => $collections,
        'base_id' => 'test',
        'temporary_upload_mode' => 'false',
    ]);
    if ($user) {
        $request->setUserResolver(fn () => $user);
    }
    $request->setLaravelSession(app('session.store'));

    return $request;
}

function createDestroyRequest($model, $user = null, $collections = ['images'])
{
    $request = DestroyRequest::create('/delete', 'POST', [
        'model_type' => get_class($model),
        'model_id' => $model->id,
        'collections' => $collections,
        'base_id' => 'test',
    ]);
    if ($user) {
        $request->setUserResolver(fn () => $user);
    }
    $request->setLaravelSession(app('session.store'));

    return $request;
}

function createEditRequest($model, $user = null, $collections = ['images'])
{
    $request = StoreUpdatedMediaRequest::create('/edit', 'POST', [
        'model_type' => get_class($model),
        'model_id' => $model->id,
        'collection' => $collections[0], // singular in StoreUpdatedMediaRequest
        'collections' => $collections,
        'base_id' => 'test',
        'temporary_upload_mode' => 'false',
        'medium_id' => '1',
    ]);
    if ($user) {
        $request->setUserResolver(fn () => $user);
    }
    $request->setLaravelSession(app('session.store'));

    return $request;
}
