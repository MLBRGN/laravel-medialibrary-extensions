# Laravel Media Library Extensions Reference

Complete reference for `mlbrgn/laravel-medialibrary-extensions`.

## Testing

- This package uses **Pest v4**.
- The package must be tested using `composer test` from inside the `packages/mlbrgn/laravel-medialibrary-extensions` directory.
- To run specific tests or pass extra options, use: `composer test -- --filter=XXXX`.
- When writing browser tests, never use `browse()`. Instead, use `visit()`.

## Media Library Extensions Features

### Extended Interfaces and Traits

Use `HasMediaExtended` and `InteractsWithMediaExtended` to unlock advanced features like data source switching and better temporary upload integration.

```php
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended;

class Post extends Model implements HasMediaExtended
{
    use InteractsWithMediaExtended;
}
```

### Temporary Uploads

The `TemporaryUpload` model handles files uploaded before the parent model is saved.

```php
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

// Get for current session and collection
$uploads = TemporaryUpload::getForCurrentSession('images');

// Filter by instance ID (for multiple components on one page)
$uploads = TemporaryUpload::getForCurrentSession('images', 'media-manager-1');

// Scope by data source
$uploads = TemporaryUpload::forDataSource('tenant-a')->get();
```

### Data Source Resolver

The `DataSourceResolver` helps switching database connections for media operations.

```php
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

$resolver = app(DataSourceResolver::class);
$connection = $resolver->resolveConnection('data-source-name');
```

## Advanced Debugging

### Component Scoping
The package uses a `DebugManager` to track nested components during rendering. Primary components (Managers, Carousels, Labs) push their unique ID as a scope before rendering sub-components.

- Use `DebugManager::pushScope($id)` before rendering.
- Use `DebugManager::popScope($id)` after rendering (handled automatically by `BaseComponent`).

### Debug UI
The debug panel (`x-mle-shared-debug`) displays all registered sub-components for the current main component. It is only rendered and updated if the `medialibrary-extensions.debug` config is enabled.

## Component Architecture

### BaseComponent & Rendering
All view components should inherit from `BaseComponent`. Use `renderView()` in the `render()` method to automate scope management and theme-aware view resolution.

```php
public function render(): View
{
    return $this->renderView('components.media-manager');
}
```

### Options & Config Synchronization
Components using the `InteractsWithOptionsAndConfig` trait should call `resolveConfig()` in the constructor to merge user-passed options with the global configuration.

## XHR & Dynamic Updates

### Preserving State
When refreshing media or previews via XHR, always pass `theme` and `data_source` parameters to ensure the backend renders the correct view and uses the correct database connection.

### Debug Synchronization
If debugging is enabled, XHR responses can include an `updatedDebugHtml` key to refresh the "All Registered Components" list in the frontend.

## Installation & Deployment

### Idempotent Migrations
The `MediaLibraryExtensionsServiceProvider` protects against redundant migration files by checking for existing files with matching base names in the application's `database/migrations` directory before registering them for publishing.

