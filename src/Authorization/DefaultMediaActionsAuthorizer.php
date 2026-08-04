<?php
namespace Mlbrgn\MediaLibraryExtensions\Authorization;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Interfaces\MediaActionsAuthorizer;

final class DefaultMediaActionsAuthorizer implements MediaActionsAuthorizer
{
    public function allows(
        string $action,
        ?Authenticatable $user,
        HasMediaExtended $model,
    ): bool {

        $ability = $action.'Media';

        $policy = Gate::getPolicyFor($model);

        if (! $policy) {
            return true;
        }

        if (! method_exists($policy, $ability)) {
            return true;
        }

        return Gate::forUser($user)
            ->allows($ability, $model);
    }
}
