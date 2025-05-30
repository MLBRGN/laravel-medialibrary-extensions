# Documentation

## override Gate

// app/Providers/AuthServiceProvider.php

use App\Policies\CustomMediaPolicy;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

public function boot()
{
$this->registerPolicies();

    Gate::policy(Media::class, CustomMediaPolicy::class);
}

or publish policies class

```shell
php artisan vendor:publish --tag=media-policy
```

## Customizing Colors

You can override the default color scheme by defining the following CSS variables in your app:

    --mlbrgn-mle-color-primary: #ffffff;
    --mlbrgn-mle-color-secondary: #ffffff;
    --mlbrgn-mle-color-accent: #ffffff;
    --mlbrgn-mle-container-light-bg: #ffffff;
    --mlbrgn-mle-container-ligher-bg: #ffffff;
