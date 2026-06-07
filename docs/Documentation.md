# Documentation

## Extended Media Library Usage

### Interfaces and Traits

To use the extended features of this package, your models should implement `HasMediaExtended` and use the `InteractsWithMediaExtended` trait instead of the default Spatie ones.

```php
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements HasMediaExtended
{
    use InteractsWithMediaExtended;
}
```

### Temporary Uploads

The `TemporaryUpload` model allows you to handle file uploads before the parent model is created. This is useful for "Create" forms.

```php
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

// Get temporary uploads for the current session and a specific collection
$uploads = TemporaryUpload::getForCurrentSession('images');

// Filter by a specific media manager instance ID
$uploads = TemporaryUpload::getForCurrentSession('images', 'media-manager-123');
```

### YouTube Videos

You can store YouTube videos as media items. The package provides actions to handle this.

```php
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoPermanentAction;

$action = app(StoreYouTubeVideoPermanentAction::class);
$media = $action->execute($request); // Expects 'youtube_url' and model details
```

### Media Reordering

You can set a specific media item or temporary upload as the "first" item in a collection.

```php
use Mlbrgn\MediaLibraryExtensions\Actions\SetMediaAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetTemporaryUploadAsFirstAction;

// For permanent media
app(SetMediaAsFirstAction::class)->execute($media);

// For temporary uploads
app(SetTemporaryUploadAsFirstAction::class)->execute($temporaryUpload);
```

### Data Sources (Multi-database)

If your application uses multiple database connections (e.g., multi-tenancy), you can use the `DataSourceResolver`.

```php
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

$resolver = app(DataSourceResolver::class);
$connection = $resolver->resolveConnection('tenant_1');

// Scoping temporary uploads by data source
$uploads = TemporaryUpload::forDataSource('tenant_1')->get();
```

## UI Components

The package provides several Blade components for media management. Most components support both `bootstrap-5` and `plain` themes.

### Media Managers

- `<x-media-manager />`: The main media manager component. It supports various modes via props (single, multiple, tinymce).
- `<x-media-manager-single />`: Wrapper for single file selection.
- `<x-media-manager-multiple />`: Wrapper for multiple file selection.
- `<x-media-manager-tinymce />`: Integration for TinyMCE editor.

### Display and Viewers

- `<x-media-carousel />`: For displaying media in a carousel. Supports both permanent media and temporary uploads.
- `<x-media-viewer />`: A modal-based viewer for media.
- `<x-image-responsive />`: Generates a responsive `<img>` tag using Spatie Media Library conversions.
- `<x-video-youtube />`: Embeds a YouTube video with proper styling.

### Media Lab and Editor

- `<x-media-lab />`: A workspace for managing and editing media.
- `<x-image-editor-modal />`: Integration with the `@mlbrgn/medialibrary-extensions` NPM package for client-side image editing.

## Advanced Configuration

### Override Gate

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
