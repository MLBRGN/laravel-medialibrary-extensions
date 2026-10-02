---
sessionId: session-261001-233746-1avj
---

# Requirements

### Overview & Goals
The goal is to improve the placeholder logic in the `ImageResponsive` component. Currently, it uses a hardcoded fallback that is difficult to customize. The new implementation will allow for a multi-level fallback strategy:
1.  **Provided Attribute**: Use the `placeholder` attribute passed to the component.
2.  **Model Fallback**: Use the model's `getFallbackMediaUrl()` if no media is available.
3.  **Global Configuration**: Use a new `placeholder_url` option in the package configuration.
4.  **No Fallback**: If none of the above are available, the component should render nothing (specifically avoiding any default "no media" icons).

### Test Blind Spot (Post-Implementation Learning)
The initial implementation used `getFallbackUrl()`, which is not a method in Spatie Media Library (the correct method is `getFallbackMediaUrl()`). This error was not caught by tests because the tests used `Mockery::mock(\Spatie\MediaLibrary\HasMedia::class)` and explicitly told it to expect `getFallbackUrl()`. Since the component called the same wrong method name as the mock was set up for, the test passed despite the code being incorrect for real models.

### Scope
- **In Scope**:
    - Modification of `media-library-extensions.php` config.
    - Modification of `ImageResponsive` PHP component class.
    - Modification of `image-responsive.blade.php` template.
    - Addition of feature tests for the new logic.
- **Out of Scope**:
    - Changes to other media components (video, audio, etc.) unless strictly necessary for consistency.
    - Modifications to the `no-media-icon.blade.php` itself.

# Technical Design

### Proposed Changes

#### Configuration
Add a new `placeholder_url` key to `config/media-library-extensions.php`:
```php
'placeholder_url' => env('MEDIA_LIBRARY_EXTENSIONS_PLACEHOLDER_URL', null),
```

#### ImageResponsive Component Class (`src/View/Components/ImageResponsive.php`)
Update the `render()` method to resolve the placeholder:
1. Remove the current hardcoded assignment:
   ```php
   // Remove this:
   $this->placeholder ??= asset(config('medialibrary-extensions.asset_path').'/images/fallback.png');
   ```
2. Implement prioritized resolution:
   - If `$url` is empty:
     - Check if `$this->placeholder` is already set (from constructor/attribute).
     - If not, and `$this->medium` is null, and `$this->modelReference` implements `HasMedia`, use `$this->modelReference->getFallbackMediaUrl()`.
     - If still null, use `config('medialibrary-extensions.placeholder_url')`.

#### ImageResponsive View (`resources/views/components/image-responsive.blade.php`)
Update the Blade file to conditionally render the image:
```blade
@if ($url)
    <img ... src="{{ $url }}" ...>
@elseif ($placeholder)
    <img ... src="{{ $placeholder }}" ...>
@endif
```
This change prevents the rendering of an empty `<img>` tag or any default icon when no media or placeholder is available.

### Architecture Diagram
The data flow for placeholder resolution:
```mermaid
graph TD
    A[Start Render] --> B{URL exists?}
    B -- Yes --> C[Render Image with URL]
    B -- No --> D{Attribute placeholder exists?}
    D -- Yes --> E[Render Image with Attribute Placeholder]
    D -- No --> F{Model fallback exists?}
    F -- Yes --> G[Render Image with Model Fallback (getFallbackMediaUrl)]
    F -- No --> H{Config placeholder exists?}
    H -- Yes --> I[Render Image with Config Placeholder]
    H -- No --> J[Render Nothing]
```

### Risks
 - **Model Instance**: `modelReference` might be a class name instead of an instance in some cases (e.g., `TemporaryUpload`). The code must check if it's an instance of `HasMedia` before calling `getFallbackMediaUrl()`.
- **Spatie Media Library Version**: Different versions might have slight differences in `getFallbackMediaUrl()`. The implementation will assume the standard Spatie interface.
- **Mock Accuracy**: Tests using mocks must be careful to use the actual method names from the Spatie API.

# Testing

### Validation Approach
Verification will be done via automated feature tests and manual inspection of the rendered HTML. To prevent future regressions, tests should use real Eloquent models that implement `HasMedia` where possible, or mocks must be strictly verified against the Spatie interface.

### Key Scenarios
1.  **Attribute Priority**: Pass a `placeholder` attribute and verify it's used even if a config or model fallback exists.
2.  **Model Fallback**: Set up a model with a fallback URL, pass it as `modelReference` with `medium` as null, and verify the fallback URL is used.
3.  **Config Fallback**: Set a `placeholder_url` in config and verify it's used when no attribute or model fallback is available.
4.  **Null Fallback**: Verify that when all sources are null, no `<img>` tag and no `@include('...no-media-icon')` is rendered.

# Delivery Steps

### ✓ Step 1: Add placeholder_url to configuration
Update `config/media-library-extensions.php` to include the `placeholder_url` key.

- Add `placeholder_url` key to the configuration array.
- Use `env('MEDIA_LIBRARY_EXTENSIONS_PLACEHOLDER_URL', null)` as the default value.
- Place it in a logical section, e.g., near `asset_path`.

### ✓ Step 2: Implement placeholder fallback logic in ImageResponsive component class
Update the `render()` method in `src/View/Components/ImageResponsive.php` to handle placeholders according to the new requirements.

- Remove the hardcoded default placeholder setting (`asset(...)`).
- Implement the fallback logic:
    - If `medium` is null and `placeholder` is not set:
        - Attempt to get the fallback URL from `modelReference` (if it implements `HasMedia`) using the first collection from `collections` via `getFallbackMediaUrl()`.
    - If `placeholder` is still null, use `config('medialibrary-extensions.placeholder_url')`.
- Ensure the placeholder is resolved only if the primary `url` is empty.

### ✓ Step 3: Update ImageResponsive Blade view for conditional rendering
Update `resources/views/components/image-responsive.blade.php` to handle cases where neither an image nor a placeholder is available.

- Modify the template to only render the `<img>` tag if either `$url` or `$placeholder` is truthy.
- Replace the `@else` block with an `@elseif ($placeholder)` block to ensure the fallback image is only shown when a valid placeholder exists.
- This ensures that if all fallbacks are null, no image or icon (specifically not the `no-media-icon`) is rendered.

### ✓ Step 4: Verify placeholder logic with tests
Add test cases to `tests/Feature/Components/ImageResponsiveTest.php` to verify the new placeholder behavior.

- Add a test for using a provided `placeholder` attribute.
- Add a test for model-based fallback when `medium` is null.
- Add a test for configuration-based fallback.
- Add a test verifying that nothing is rendered when all fallbacks are null.

### ✓ Step 5: Implement and run browser tests
Add a browser test to verify placeholder rendering in a real browser environment.

- Add a test route `/test-placeholders` in `tests/BrowserTestCase.php` to render all placeholder scenarios.
- Create `tests/Browser/ImageResponsivePlaceholderTest.php` to visit the route and assert the presence and correct `src` of images.
- Verify that the `no-media-icon` SVG is not rendered when all fallbacks are null.