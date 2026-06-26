<?php

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Session;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreUpdatedMediaRequest;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\User;

beforeEach(function () {
    Session::start();
});

it('authorizes media uploads via global toggle', function () {
    // 1. Allowed by default
    $model = Blog::create(['title' => 'Test']);
    $request = createStoreRequest($model);
    expect($request->authorize())->toBeTrue();

    // 2. Denied via static override
    $deniedModel = DeniedUploadBlog::create(['title' => 'Denied Upload']);
    $request = createStoreRequest($deniedModel);
    expect($request->authorize())->toBeFalse();
});

class DeniedUploadBlog extends Blog
{
    public static function allowsMediaUploads(): bool
    {
        return false;
    }

    public function getTable()
    {
        return 'blogs';
    }
}

class AuthorizedBlog extends Blog
{
    public function allowsMediaUploadFrom(?Authenticatable $user): bool
    {
        return $user && $user->getAuthIdentifier() === 1;
    }

    public function allowsMediaDeletesFrom(?Authenticatable $user): bool
    {
        return $user && $user->getAuthIdentifier() === 1;
    }

    public function allowsMediaEditsFrom(?Authenticatable $user): bool
    {
        return $user && $user->getAuthIdentifier() === 1;
    }

    public function getTable()
    {
        return 'blogs';
    }
}

class CollectionRestrictedBlog extends Blog
{
    public function allowedMediaCollections(): array
    {
        return ['allowed-collection'];
    }

    public function getTable()
    {
        return 'blogs';
    }
}

it('authorizes media uploads via user check', function () {
    $user = new User(['id' => 1]);
    $model = AuthorizedBlog::create(['title' => 'User Check']);

    // 1. Authorized user
    $request = createStoreRequest($model, $user);
    expect($request->authorize())->toBeTrue();

    // 2. Unauthorized user
    $otherUser = new User(['id' => 2]);
    $request = createStoreRequest($model, $otherUser);
    expect($request->authorize())->toBeFalse();
});

it('authorizes media deletes via global toggle', function () {
    $model = Blog::create(['title' => 'Delete Toggle']);
    $request = createDestroyRequest($model);
    expect($request->authorize())->toBeTrue();

    // Use a real class for toggle too
    $deniedModel = DeniedDeleteBlog::create(['title' => 'Denied Delete']);
    $request = createDestroyRequest($deniedModel);
    expect($request->authorize())->toBeFalse();
});

class DeniedDeleteBlog extends Blog
{
    public static function allowsMediaDeletes(): bool
    {
        return false;
    }

    public function getTable()
    {
        return 'blogs';
    }
}

it('authorizes media deletes via user check', function () {
    $user = new User(['id' => 1]);
    $model = AuthorizedBlog::create(['title' => 'Delete User Check']);

    $request = createDestroyRequest($model, $user);
    expect($request->authorize())->toBeTrue();

    $otherUser = new User(['id' => 2]);
    $request = createDestroyRequest($model, $otherUser);
    expect($request->authorize())->toBeFalse();
});

it('authorizes media edits via global toggle', function () {
    $model = Blog::create(['title' => 'Edit Toggle']);
    $request = createEditRequest($model);
    expect($request->authorize())->toBeTrue();

    $deniedModel = DeniedEditBlog::create(['title' => 'Denied Edit']);
    $request = createEditRequest($deniedModel);
    expect($request->authorize())->toBeFalse();
});

class DeniedEditBlog extends Blog
{
    public static function allowsMediaEdits(): bool
    {
        return false;
    }

    public function getTable()
    {
        return 'blogs';
    }
}

it('authorizes media edits via user check', function () {
    $user = new User(['id' => 1]);
    $model = AuthorizedBlog::create(['title' => 'Edit User Check']);

    $request = createEditRequest($model, $user);
    expect($request->authorize())->toBeTrue();

    $otherUser = new User(['id' => 2]);
    $request = createEditRequest($model, $otherUser);
    expect($request->authorize())->toBeFalse();
});

it('restricts media actions by collection', function () {
    $model = CollectionRestrictedBlog::create(['title' => 'Collection Restricted']);

    // 1. Allowed collection
    $request = createStoreRequest($model, null, ['allowed-collection']);
    expect($request->authorize())->toBeTrue();

    // 2. Disallowed collection
    $request = createStoreRequest($model, null, ['forbidden-collection']);
    expect($request->authorize())->toBeFalse();

    // 3. Mixed collections (denied if any is forbidden)
    $request = createStoreRequest($model, null, ['allowed-collection', 'forbidden-collection']);
    expect($request->authorize())->toBeFalse();
});

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
