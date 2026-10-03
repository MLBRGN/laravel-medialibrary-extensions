### Bug Report: Crash when using `x-mle-media-responsive` with `expandable-in-modal` and null medium

#### Description
The application crashes with a `TypeError` when the `<x-mle-media-responsive>` (or legacy `<x-mle-image-responsive>`) component is used with `:expandable-in-modal="true"` but the `:medium` passed to it is `null`. This typically happens when a developer uses `getFirstMediaUrl()` to check for an image (which might return a fallback URL), but then passes `getFirstMedia()` (which returns `null`) to the component.

#### Error Message
`Mlbrgn\MediaLibraryExtensions\Services\MediaModelResolver::resolveModelReference(): Argument #1 ($modelReference) must be of type Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended|string, null given, called in .../vendor/mlbrgn/laravel-medialibrary-extensions/src/View/Components/BaseMediaComponent.php on line 62`

#### Affected URL
`http://mlbrgn.net.test/auth/prikbord-item/gezelschap-voor-koffie-thee-gevraagd`

#### Reproduction Steps
1. Create a model that implements `HasMediaExtended` and has a media collection with a fallback URL.
2. Create an instance of this model with NO media in that collection.
3. In a Blade view, render the component like this:
```blade
@php
    $mainImage = $item->getFirstMediaUrl('collection-name'); // Returns fallback URL, so true
@endphp

@if($mainImage)
    <x-mle-media-responsive
        id="my-id"
        :medium="$item->getFirstMedia('collection-name')" {{-- Returns null --}}
        :expandable-in-modal="true"
        placeholder="/images/placeholder.jpeg"
    />
@endif
```
4. Visit the page. The application will crash.

#### Technical Analysis
1. **Component Initialization:** `MediaResponsive` is initialized. Since `:medium` is `null` and `:model-reference` is not provided, `$this->modelReference` remains `null`.
2. **Blade Rendering:** The component's blade template (`media-responsive.blade.php`) tries to render `<x-mle-media-modal>` because `expandableInModal` is true.
3. **Modal Initialization:** It passes `:model-reference="$modelReference"` (which is `null`) to `MediaModal`.
4. **BaseMediaComponent:** `MediaModal` extends `BaseMediaComponent`. Its constructor calls `resolveModel($modelReference)`, which then calls `MediaModelResolver::resolveModelReference(null)`.
5. **Resolution Failure:** `MediaModelResolver::resolveModelReference` has a strict type hint `HasMediaExtended|string` for the first argument, causing the crash when `null` is passed.

#### Root Cause in `mlbrgn/laravel-medialibrary-extensions`
The `MediaResponsive` component attempts to "guess" the model reference from the medium if it's not explicitly provided:
```php
// MediaResponsive.php
if ($this->expandableInModal && $this->modelReference === null && $this->medium) {
    if ($this->medium instanceof Media) {
        $this->modelReference = $this->medium->model;
    }
    // ...
}
```
If `medium` is `null`, this inference fails, and `null` is propagated to `MediaModal`, which cannot handle it.

Similarly, other components extending `BaseMediaComponent` (like `MediaGallery` and `MediaModal` itself) will crash if `modelReference` is passed as `null`, as they all call `MediaModelResolver::resolveModelReference()` which has a strict type hint.

#### Recommended Fix
1. **In `MediaModelResolver`:** Allow `null` for `$modelReference` in `resolveModelReference` or handle it gracefully in `BaseMediaComponent`. Returning a "null" `ResolvedModel` or allowing the property to be nullable would prevent the crash.
2. **In `MediaResponsive`:** If `expandableInModal` is true and `medium` is null, it should probably either disable the modal or require an explicit `model-reference`. Alternatively, it could try to infer the model if a `model` attribute was passed to the parent component, but `MediaResponsive` currently doesn't take the model itself as a primary attribute, only the `medium`.
