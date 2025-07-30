<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection PhpUnusedParameterInspection */

namespace Mlbrgn\MediaLibraryExtensions\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPolicy
{
    public function uploadMedia(?Authenticatable $user): bool
    {
        return true;
        //        return $user !== null;
    }

    public function deleteMedia(?Authenticatable $user, Media $media): bool
    {
        return true;
        //        return $user !== null && $user->id === $media->model->user_id;
    }

    public function reorderMedia(?Authenticatable $user): bool
    {
        return true;
        //        return $user !== null;
    }
}
