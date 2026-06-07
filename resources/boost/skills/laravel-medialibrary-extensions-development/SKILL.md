---
name: laravel-medialibrary-extensions-development
description: Build and work with mlbrgn/laravel-medialibrary-extensions features, including temporary uploads, multi-connection data sources, and enhanced media management.
license: MIT
metadata:
  author: Spatie
---

# Media Library Extensions Development

## Overview

Extension for `spatie/laravel-medialibrary` that adds temporary uploads, multi-connection data sources, media uploader components, galleries, and an image editor.

## Testing

- The package can be tested using `composer test`.
- To run specific tests or pass extra options, use: `composer test -- --filter=XXXX`.

## When to Activate

- Activate when working with file uploads, media attachments, or image processing in Laravel.
- Activate when code references `HasMedia`, `HasMediaExtended`, `InteractsWithMedia`, `InteractsWithMediaExtended`, the `Media` model, or `TemporaryUpload`.
- Activate when working with media uploader components or the media manager.

## Scope

- In scope: temporary uploads, data source switching, media uploader components, galleries, image editor, and specific extension features.
- Out of scope: non-Laravel frameworks, general file storage without Eloquent association.

## Workflow

1. Identify the task (temporary upload logic, model setup, component usage, etc.).
2. Read `references/laravel-medialibrary-extensions-guide.md` for specific extension patterns.
3. Use `HasMediaExtended` and `InteractsWithMediaExtended` for full feature support.

## Core Concepts

### Model Setup (Extensions)

Use `HasMediaExtended` and `InteractsWithMediaExtended` to support data sources and temporary uploads:

```php
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended;

class Post extends Model implements HasMediaExtended
{
    use InteractsWithMediaExtended;
}
```

### Temporary Uploads

Retrieve temporary uploads for the current session:

```php
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

$uploads = TemporaryUpload::getForCurrentSession('images');
```

### Data Sources

Switch database connections dynamically:

```php
$uploads = TemporaryUpload::forDataSource('tenant-1')->get();
```

## Do and Don't

Do:
- Use `InteractsWithMediaExtended` instead of the base Spatie trait when using this package's features.
- Check `TemporaryUpload` before persisting media from components.
- Use the provided Blade components for media management.

Don't:
- Don't bypass the `DataSourceResolver`.
- Don't forget to handle `session_id` when working with temporary uploads.

## References

- `references/laravel-medialibrary-extensions-guide.md`
