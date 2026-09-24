---
sessionId: session-260924-204344-3iwl
---

# Requirements

### Overview & Goals
The goal is to add support for opening the first available media in a modal when the `MediaFirstAvailable` component is clicked. This functionality already exists in other media components (like `media-preview-item`) and should be replicated here to maintain consistency.

### Functional Requirements
- When the `expandableInModal` option is set to `true`, clicking the `MediaFirstAvailable` component should open a modal showing the media.
- This behavior must be optional and default to `false`.
- The modal should correctly display the media associated with the model's collections.
- The component should use the existing `x-mle-media-modal` component for the modal implementation.

### Scope
- **In Scope**:
    - Modification of `Mlbrgn\MediaLibraryExtensions\View\Components\MediaFirstAvailable` (if needed for data passing).
    - Modification of `resources/views/components/media-first-available.blade.php` to add trigger attributes and include the modal component.
    - Addition of feature tests to verify the new functionality.
- **Out of Scope**:
    - Changes to the `media-modal` or `media-carousel` components themselves.
    - Adding modal support to other components not mentioned.

# Technical Design

### Current Implementation
The `MediaFirstAvailable` component currently renders a `MediaViewer` for the first medium it finds in the specified collections. While it has an `expandableInModal` property, it is only passed down to `MediaViewer` (which uses it to show a zoom cursor) but doesn't actually trigger a modal.

### Proposed Changes
1.  **View Update**: In `resources/views/components/media-first-available.blade.php`, we will wrap the component content or update the existing wrapper to include Bootstrap modal trigger attributes when `expandableInModal` is true.
2.  **Modal Inclusion**: We will include `<x-mle-media-modal ... />` at the end of the template if `expandableInModal` is enabled.
3.  **Data Flow**: Ensure that the modal receives the correct `modelReference` and `collections` to display the relevant media.

### Technical Details
- **Trigger Attributes**:
  - `data-bs-toggle="modal"`
  - `data-bs-target="#{{ $id }}-mod"` (using the `mod` suffix which is standard for `MediaModal` in this package).
- **Modal Component**:
  ```blade
  @if($expandableInModal && $medium)
      <x-mle-media-modal
          :id="$id"
          :model-reference="$modelReference"
          :collections="$collections"
          :options="$getOptions()"
          :data-source="$dataSource"
      />
  @endif
  ```

### Files Involved
- `src/View/Components/MediaFirstAvailable.php`
- `resources/views/components/media-first-available.blade.php`
- `tests/Feature/Components/MediaFirstAvailableTest.php`

# Testing

### Validation Approach
I will verify the changes by adding automated feature tests that check for the presence of specific HTML attributes and components in the rendered output.

### Key Scenarios
1.  **Modal Disabled (Default)**:
    - Verify that `data-bs-toggle="modal"` is NOT present.
    - Verify that the modal component is NOT rendered.
2.  **Modal Enabled**:
    - Verify that `data-bs-toggle="modal"` IS present on the wrapper.
    - Verify that `data-bs-target` correctly points to the modal ID.
    - Verify that the `x-mle-media-modal` component (or its resulting HTML) is present in the output.
3.  **No Media**:
    - Verify that even if `expandableInModal` is true, if no medium is found, no modal trigger or component is rendered.

# Delivery Steps

### ✓ Step 1: Update MediaFirstAvailable view for modal support
Update the `MediaFirstAvailable` Blade view to support the modal trigger.
- Add `data-bs-toggle="modal"` and `data-bs-target` attributes to the component wrapper when `expandableInModal` is true.
- Conditionally include the `x-mle-media-modal` component at the end of the view if `expandableInModal` is true and a medium is available.
- Ensure the modal is passed the correct properties (`id`, `model-reference`, `collections`, `options`, `dataSource`).

### ✓ Step 2: Ensure data availability in MediaFirstAvailable component
Update the `MediaFirstAvailable` component class to ensure all necessary data is available to the view.
- Ensure `collections` and `modelReference` are properly handled if not already passed to the view (they are currently public, so they should be available, but I will double-check).
- Verify that `expandableInModal` is correctly passed to `MediaViewer`.

### ✓ Step 3: Add tests for MediaFirstAvailable modal support
Add a new test case to `MediaFirstAvailableTest.php` to verify the modal functionality.
- Test that the component renders the expected data attributes when `expandableInModal` is set to true.
- Test that the modal component is present in the rendered HTML when `expandableInModal` is true.
- Test that the component remains unchanged (no modal attributes/component) when `expandableInModal` is false (default).