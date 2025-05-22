<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Policies;

use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;

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
