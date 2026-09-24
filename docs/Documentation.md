# Laravel Media Library Extensions

An extension for Spatie Laravel Media Library that provides enhanced media management, temporary uploads, multi-database support, and a suite of Blade components.

---

## 1. Getting Started

### Installation

```shell
composer require mlbrgn/laravel-medialibrary-extensions
```

Publish the config file:

```shell
php artisan vendor:publish --tag=medialibrary-extensions-config
```

### Core Requirements

To use the extended features, your models must implement `HasMediaExtended` and use the `InteractsWithMediaExtended` trait.

```php
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements HasMediaExtended
{
    use InteractsWithMediaExtended;
}
```

---

## 2. UI Components

The package provides a rich set of Blade components for media management and display. All components support `bootstrap-5` (default) and `plain` themes.

### Media Managers

Media managers handle file uploads, deletions, and selection.

- `<x-mle-media-manager />`: The universal manager.
- `<x-mle-media-manager-single />`: Optimized for a single file (e.g., profile picture).
- `<x-mle-media-manager-multiple />`: For galleries or multi-file attachments.
- `<x-mle-media-manager-tinymce />`: Integration for the TinyMCE editor.

**Common Props:**
- `model-reference` (Blade attribute) / `modelReference` (constructor prop): The model instance or class name (for create forms).
- `collections`: Array of media collections to manage (e.g., `['images']`).
- `multiple`: Boolean, allow multiple files.
- `readonly`: Boolean, disable all actions.
- `required`: Boolean, enforces at least one item (shortcut for `minMediaCount: 1`).
- `name`: (string) The name of the hidden count field (defaults to `id`). Used for Laravel validation.
- `options`: Array of UI overrides (see Configuration).

### Display Components

- `<x-mle-media-carousel />`: A responsive carousel supporting images and videos.
- `<x-mle-media-viewer />`: A modal-based viewer for full-screen media inspection.
- `<x-mle-image-responsive />`: Generates responsive `<img>` tags with conversions.
- `<x-mle-video-youtube />`: Responsive YouTube embed.

### Advanced Components

- `<x-mle-media-lab />`: A workspace for advanced media editing.
- `<x-mle-image-editor-modal />`: Client-side image cropping and filters.

---

## 3. Temporary Uploads & Promotion

The package excels at handling "Create" forms where the model doesn't exist yet.

### The Lifecycle
1. **XHR Upload**: Files are uploaded to a temporary disk (`media_temporary`).
2. **Client Token**: A `client_token` (ULID) is assigned to the browser session.
3. **Promotion**: When the model is finally saved, temporary uploads are "promoted" to permanent media.

### Automatic Promotion
Promotion happens automatically on Eloquent `created` or `updated` events if you use `InteractsWithMediaExtended`.

### HTML URL Replacement
If you insert temporary image URLs into an HTML editor (like TinyMCE), the package can automatically swap them for permanent URLs upon promotion.

Define which fields to scan in your model:

```php
public function getHtmlEditorFields(): array
{
    return ['content', 'description'];
}
```

### Manual Promotion
For background jobs or custom logic:

```php
use Mlbrgn\MediaLibraryExtensions\Services\TemporaryUploadPromoter;

app(TemporaryUploadPromoter::class)->promoteAllForModel($model, $instanceId, $clientToken);
```

---

## 4. Advanced Features

### YouTube Integration
Store YouTube URLs as media items.
```php
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoPermanentAction;

$media = app(StoreYouTubeVideoPermanentAction::class)->execute($request);
```

### Media Reordering
Set a specific item as the "main" (first) item in a collection.
```php
app(SetMediaAsFirstAction::class)->execute($media);
```

### Multi-Database (Data Sources)
Scope media and temporary uploads to different database connections.
```php
// Runtime usage
TemporaryUpload::forDataSource('tenant_1')->get();

// In requests/components, pass the same data source key
// e.g. data_source=tenant_1
```

---

## 5. Authorization

Control who can upload, delete, or edit media.

### Static Toggles
```php
public static function allowsMediaUploads(): bool { return true; }
```

### Instance Authorization
```php
public function allowsMediaUploadFrom(?Authenticatable $user, HasMediaExtended $model): bool
{
    return $user && $user->isAdmin();
}
```

---

## 6. Configuration & Customization

### Configuration Options
Pass these via the `options` prop to components:
- `maxMediaCount`: (int) Maximum number of items allowed.
- `minMediaCount`: (int) Minimum number of items required.
- `showDestroyButton`: (bool) Show/hide delete icon.
- `showSetAsFirstButton`: (bool) Show/hide "set as main" icon.
- `useXhr`: (bool) Enable/disable AJAX uploads.
- `theme`: `'bootstrap-5'` or `'plain'`.

### Customizing Styles
Override CSS variables in your application:
```css
:root {
    --mlbrgn-mle-color-primary: #3490dc;
    --mlbrgn-mle-container-light-bg: #f8fafc;
}
```

### Customizing Icons
Define your preferred icons in `config/media-library-extensions.php`. It supports `blade-icons`.

---

## 7. Troubleshooting

### Temporary files not being promoted?
- Ensure the `client_token` is being sent with your form (the package usually handles this via cookies).
- Check if your model implements `HasMediaExtended` and uses the trait.

### Assets not loading?
- Run `php artisan vendor:publish --tag=medialibrary-extensions-assets`.
- If using Vite, ensure you have followed the [Vite Setup Guide](./vite-package-setup.md).

---

## 8. Originals (archiving, replacement, restoration)

This package can archive the original uploaded file to a dedicated filesystem disk and later restore it or reuse it across replacements.

Quick facts:
- Storage location: files are saved to the disk configured at `medialibrary-extensions.media_disks.originals` under the path `<media id>/<file_name>`.
- When archived: on `MediaHasBeenAddedEvent` (if the owning model allows originals), the package streams the file from the media’s path and writes it to the originals disk. It records helpful flags on the media’s `custom_properties`.
- Database: there is no separate table; originals are indicated by flags in `custom_properties`. A convenience model `OriginalMedia` extends Spatie’s `Media` while using the same `media` table.
- Restore: the archived file can be copied back to the media’s actual storage location, and conversions are marked for regeneration.

See the detailed guide: [Originals – storage and lifecycle](./originals.md)

---

## 9. Validation

The package integrates with Laravel's native validation system to enforce media requirements.

### Live Count Synchronization
When a `name` is provided to a media manager, it renders a hidden input field:
```html
<input type="hidden" name="image" value="0" data-mle-media-count="blog-main">
```
This value is automatically updated via JavaScript whenever files are uploaded or deleted.

### Server-Side Rules
To securely validate requirements (ignoring the client-side hidden field and checking the database/temporary storage), use the provided rules in your `FormRequest` or controller.

#### `MediaCount` (Recommended)
A unified rule with a fluent API to handle minimum, maximum, and exact counts. It automatically resolves the "effective" media count by summing permanent media on the model, temporary uploads for the current instance/client, and any direct uploads in the request.

```php
use Mlbrgn\MediaLibraryExtensions\Rules\MediaCount;

public function rules(): array
{
    return [
        'gallery' => [
            (new MediaCount($this->blog, ['images']))
                ->min(1)
                ->max(5)
        ],
        'profile' => [
            (new MediaCount($this->user, ['avatar']))
                ->exactly(1)
        ]
    ];
}
```

Methods:
- `min(int $value)`: Minimum items required.
- `max(int $value)`: Maximum items allowed.
- `exactly(int $value)`: Exact number of items required.
- `multiple(bool $value)`: Use plural or singular error messages (defaults to `true`).

#### `MinMediaCount`
Enforces a minimum number of items. This is a thin wrapper around `MediaCount` for backward compatibility.

```php
use Mlbrgn\MediaLibraryExtensions\Rules\MinMediaCount;

// For existing models (permanent + temporary)
'gallery' => [new MinMediaCount($this->blog, ['images'], 3)],
```

#### `MaxMediaCount`
Enforces a maximum number of items. This now counts both permanent media AND temporary uploads to prevent exceeding capacity during a session.

```php
use Mlbrgn\MediaLibraryExtensions\Rules\MaxMediaCount;

'images' => [new MaxMediaCount($this->blog, ['images'], 10)],
```

#### `MaxTemporaryUploadCount`
A specialized rule that enforces a maximum count of **only** temporary uploads for a specific instance, ignoring permanent media.

```php
use Mlbrgn\MediaLibraryExtensions\Rules\MaxTemporaryUploadCount;

'images' => [new MaxTemporaryUploadCount(10, collections: ['images'])],
```

### Authoritative Counting & `mle_instance_map`
The hidden field named by the component (e.g., `gallery`) sends a "client-side hint" of the current count. For security, the server-side rules **do not trust** this numeric value. Instead, they perform an authoritative count from the database.

To correctly associate a form field with its temporary uploads when multiple managers are on the same page, the components automatically include an `mle_instance_map`.

**Form Submission Example:**
- `_token`: `...`
- `gallery`: `2` (client-side hint)
- `mle_instance_map[gallery]`: `C2E036E7...` (the actual `instanceId`)

The validation rules use this map to resolve the correct `instanceId` and count only the relevant temporary uploads.

### Form Isolation
When multiple media managers are used in a single form, the package automatically isolates their internal XHR fields (like client tokens and instance IDs) to prevent them from "leaking" into your parent form's submission. 

Internal fields are moved to an isolated hidden form in the DOM using the HTML `form` attribute, while the validation metadata (`mle_instance_map`) remains in the parent form. This ensures that `request()->all()` in your controller stays clean and contains only the data you expect.

### Displaying Errors
If validation fails, the component automatically displays the error message in an alert box if it's nested inside a form and the `name` matches the validation key.

```blade
<x-mle-media-manager-single
    id="blog-main"
    name="image"
    required
    :model-reference="$blog"
    :collections="['image' => 'blog-main']"
/>
```
