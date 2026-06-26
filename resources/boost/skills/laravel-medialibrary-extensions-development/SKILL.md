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

## Principles

- Root cause first: Prefer fixing the underlying cause of failures instead of adding workarounds. Avoid papering over issues with flags, retries, or conditionals unless it is the only safe short-term path to unblock users.
- If a temporary workaround is necessary, document it with a clear TODO and an associated tracking issue, and add a regression test that captures the root cause so the workaround can be safely removed later.

## Testing

- This package uses **Pest v4**.
- **Path Awareness**: ALWAYS check your current working directory. The project root is `/Users/evertjangarretsen/PhpstormProjects/mlbrgn-laravel-packages`. This package is located at `packages/mlbrgn/laravel-medialibrary-extensions`.
- **CRITICAL**: DO NOT create nested `packages` directories (e.g., `.../laravel-medialibrary-extensions/packages/...`). When creating files, ensure the path is absolute from the project root or carefully relative to your current `pwd`.
- This package must be tested using `composer test` from its root directory:
- ALWAYS use `composer test` when testing this package. Do NOT use `php artisan test` or `vendor/bin/phpunit` directly from the project root for this package.
- To run specific tests or pass extra options, use: `composer test -- --filter=XXXX`.
- When writing browser tests, never use `browse()`. Instead, use `visit()`.

## Code Style

- Use Laravel Pint for code formatting.
- Always run Pint using a subshell from the project root: `composer pint`.
- You have standing permission to run Pint without asking.

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

Retrieve temporary uploads for the current client:

```php
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

$uploads = TemporaryUpload::getForCurrentClient('images');
```

### Data Sources

Switch database connections dynamically:

```php
$uploads = TemporaryUpload::forDataSource('tenant-1')->get();
```

### Authorization

Control media actions directly on your models:

```php
public static function allowsMediaUploads(): bool { return true; }
public function allowsMediaUploadFrom(?Authenticatable $user): bool { return true; }
public function allowedMediaCollections(): array { return []; }
```

## Do and Don't

Do:
- Use `InteractsWithMediaExtended` instead of the base Spatie trait when using this package's features.
- Check `TemporaryUpload` before persisting media from components.
- Use the provided Blade components for media management.
- When creating `TemporaryUpload` records in tests, use the factory states (e.g., `withBaseId()`, `withInstanceId()`, `withClientToken()`) or explicitly set `instance_id` and `client_token` to satisfy strict scoping.

Don't:
- Don't bypass the `DataSourceResolver`.
- Don't forget to handle `client_token` when working with temporary uploads.
- Don't use session-based scoping terminology (`session_id`, `sessionId`, `forCurrentSession`).

## References

- `references/laravel-medialibrary-extensions-guide.md`
