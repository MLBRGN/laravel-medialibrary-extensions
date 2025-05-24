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
