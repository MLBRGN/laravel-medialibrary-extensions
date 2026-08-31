<?php

namespace Mlbrgn\MediaLibraryExtensions\Interfaces;

use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

interface MediaActionsAuthorizer
{
    public function allows(
        string $action,
        ?Authenticatable $user,
        HasMediaExtended $model,
    ): bool;

    // TODO make the contract return Response instead of bool, just like Laravel's Gate:
    // then i can return
    // return Response::deny('Only the profile owner may upload media.');
    // or
    // return $response->allowed();
    //    public function authorize(
    //        string $action,
    //        ?Authenticatable $user,
    //        HasMediaExtended $model,
    //    ): Response;
}
