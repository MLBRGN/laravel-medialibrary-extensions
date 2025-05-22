<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Policies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

// TODO not used at the moment
class MediaPolicy
{
    public function uploadMedia(User $user, Model $model): bool
    {
        // default behavior (can be overridden)
        return true;
    }

    public function deleteMedia(User $user, Model $model): bool
    {
        return true;
    }
}
