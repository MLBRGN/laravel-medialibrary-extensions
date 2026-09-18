---
sessionId: session-260908-233808-8xq7
---

# Requirements

### Overview & Goals
The goal of this task is to provide a way for the `laravel-medialibrary-extensions` package to serve its CSS, JS, and image assets without requiring the host application to run `php artisan vendor:publish`. This solves the problem of "stale assets" where published files become outdated after a package update.

### Scope
- **In Scope**:
  - A dynamic asset serving route and controller.
  - Automatic versioning for cache busting.
  - Configuration to toggle between published and dynamic assets.
  - Updates to Blade components to support the new loading mechanism.
  - Maintaining existing lazy-load functionality in the JavaScript loader.
- **Out of Scope**:
  - Changing the asset build process (Vite).
  - Removing the ability to publish assets (it remains an option for performance-critical setups).

### User Stories
- **As a developer**, I want my package updates to reflect frontend changes immediately without manual publishing steps.
- **As a user**, I want the media manager to load its assets efficiently and only when needed, even if they aren't published to the public directory.


# Technical Design

### Current Implementation
Currently, the package relies on `Illuminate\ServiceProvider::publishes()` to copy compiled assets from the `dist` directory to the host's `public/vendor/mlbrgn/laravel-medialibrary-extensions` path. The frontend components then use the `asset()` helper to load these files.

### Proposed Changes
1. **Dynamic Asset Route**:
   Register a route (e.g., `mlbrgn-mle/assets/{version}/{path}`) that points to a new `AssetController`. The `{version}` segment ensures that browser caches are invalidated whenever the package is updated.

2. **AssetController**:
   A controller that reads files directly from the package's `dist` (for JS/CSS) and `resources/images` directories. It will:
   - Sanitize paths to prevent directory traversal attacks.
   - Set appropriate `Content-Type` headers based on file extensions.
   - Set aggressive caching headers (`Cache-Control: public, max-age=31536000, immutable`).

3. **Asset Helper & Config**:
   - Add a `use_published_assets` boolean to the package config.
   - Introduce a `mle_asset()` helper that generates the correct URL based on this config.

4. **Component Updates**:
   Update the `Assets` Blade component to set the `assetBasePath` to the new dynamic route. This ensures the existing JavaScript loader's lazy-loading logic continues to work seamlessly.

### File Structure Changes
- **New Files**:
  - `src/Http/Controllers/AssetController.php`
- **Modified Files**:
  - `routes/web.php`: Add the asset route.
  - `config/media-library-extensions.php`: Add asset configuration.
  - `src/View/Components/Shared/Assets.php`: Update URL generation.
  - `resources/views/components/shared/assets.blade.php`: Update loader script source.

### Architecture Diagram
```mermaid
graph TD
    HostApp[Host Application] -->|Blade Component| MLEAssets[Assets Component]
    MLEAssets -->|mle_asset()| AssetRoute[Asset Serving Route]
    AssetRoute -->|AssetController| PackageDist[Package dist/resources]
    PackageDist -->|CSS/JS/Images| Browser[User Browser]
    Browser -->|Lazy Load| AssetRoute
```

### Risks & Mitigations
- **Performance**: Serving assets through Laravel is slower than direct Nginx/Apache serving.
  - *Mitigation*: Use `immutable` cache headers and long-term expiration. The JS loader's lazy-loading also reduces the initial impact.
- **Security**: Potential for directory traversal if paths aren't sanitized.
  - *Mitigation*: Strict path validation in the `AssetController` to only allow specific directories and file types.


# Testing

### Validation Approach
- **Manual Verification**: Access the asset route directly in a browser to ensure CSS and JS files are served with correct headers.
- **Integration Test**: Update the demo pages to use dynamic assets and verify the Media Manager still functions correctly (including lazy-loaded scripts like the image editor).
- **Cache Check**: Inspect network headers in browser DevTools to confirm `Cache-Control` and versioning are working as expected.

### Key Scenarios
- **Package Update**: Change the version constant and verify that asset URLs change, forcing a browser refresh.
- **Lazy Loading**: Trigger the image editor and verify that `image-editor.js` is fetched via the dynamic route.
- **Invalid Path**: Attempt to access a file outside the allowed directories (e.g., `../../config/app.php`) and verify it returns a 404.


# Delivery Steps

###   Step 1: Implement Dynamic Asset Serving Infrastructure
Create a new controller and register a route to handle dynamic asset serving.

- Create `src/Http/Controllers/AssetController.php` with logic to serve files from the package's `dist` and `resources/images` directories.
- Implement path sanitization in the controller to prevent directory traversal.
- Add the asset serving route to `routes/web.php` using a configurable prefix.
- Ensure the route supports a version parameter for cache busting.

###   Step 2: Integrate Asset Loading Logic and Configuration
Add configuration options and a helper to manage asset loading preferences.

- Update `config/media-library-extensions.php` to include `use_published_assets` (defaulting to `false`).
- Create or update a helper function `mle_asset()` that returns either the published asset URL or the dynamic route URL based on configuration.
- Add a `VERSION` constant to the `ServiceProvider` or a dedicated version helper to facilitate cache busting.

###   Step 3: Update Blade Components and JS Loader
Modify the frontend components to utilize the new dynamic asset serving mechanism.

- Update the `Assets` view component (`src/View/Components/Shared/Assets.php`) to use the `mle_asset()` helper for the loader script and base path.
- Update `resources/views/components/shared/assets.blade.php` to ensure the correct `assetBasePath` is passed to the JavaScript loader.
- Verify that the JavaScript `media-library-loader.js` correctly resolves lazy-loaded assets through the new route.

###   Step 4: Finalize Caching, Security, and Documentation
Ensure the new asset serving method is efficient and secure.

- Implement long-term caching headers (`Cache-Control: public, max-age=31536000, immutable`) in the `AssetController`.
- Add validation to the `AssetController` to only serve allowed file types (CSS, JS, PNG, JPG, SVG, etc.).
- Update `composer.json` or documentation to reflect that publishing assets is now optional.