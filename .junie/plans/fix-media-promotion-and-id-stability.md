---
sessionId: session-260901-203320-1flo
---

# Requirements

### Overview & Goals
The goal is to resolve issues where temporary media uploads (especially from TinyMCE) fail to "promote" to permanent media items upon form submission. The implementation will be simplified by removing random identifiers and ensuring that the frontend and backend always agree on a stable `instance_id`. This is achieved by making component IDs mandatory and explicit.

### Scope
- **In Scope**:
    - Fixing TinyMCE JavaScript integration for passing `instanceId`.
    - Stabilizing component and instance IDs by removing random suffixes and random ULID fallbacks.
    - Automating the registration of `instance_id` in parent forms via JavaScript.
    - Improving the `TemporaryUploadPromoter` with a "wildcard" fallback to ensure reliability.
- **Out of Scope**:
    - Changes to the underlying Spatie Media Library core.
    - UI/UX redesign of the Media Manager.

### User Stories
- **As a Content Editor**, I want my images uploaded via TinyMCE to be correctly saved when I submit the form, without manual configuration.
- **As a Developer**, I want the system to be predictable and "just work" without having to worry about mismatched random IDs.

### Functional Requirements
- TinyMCE media picker must automatically link its uploads to the parent form.
- Components must have a mandatory, explicit `id` to ensure `instanceId` stability across multiple renders.
- The system must purge old temporary uploads that were never promoted.

# Technical Design

### Current Implementation
- **Randomness**: `InstanceManager` hashes IDs into ULID-like strings, and `BaseComponent` defaults to a random `Str::ulid()` when no ID is provided, causing instability.
- **Broken Links**: The logic to pass `instanceId` from the TinyMCE iframe to the parent form is currently commented out.
- **Stale Files**: Temporary uploads linger when the `instance_id` sent by the form doesn't match the one used during upload.

### Key Decisions
- **Mandatory Explicit IDs**: We will make the `$id` parameter mandatory in all media components. Removing random generation ensures the `instance_id` is always predictable and controlled by the developer.
- **Stable 1:1 Mapping**: `instanceId` will be identical to the component's `$id`. We will remove the `InstanceManager` hash logic to keep things transparent.
- **JS Auto-Injection**: TinyMCE's file picker will actively inject or update `<input type="hidden" name="mle_instance_ids[]">` in the parent form upon image selection.
- **Wildcard Safety Net**: If no specific `instance_id` matches during promotion, the promoter will fallback to promoting all uploads for the current `client_token`. This simplifies the requirement for exact ID matching in simple scenarios.
- **Incremental Implementation**: Changes will be applied in small, logical increments. After each logical change, existing tests will be run to ensure no regressions were introduced.

### Proposed Changes

#### JavaScript
- **`tinymce-custom-file-picker-iframe.js`**:
    - Uncomment and fix the `postMessage` logic to include `instanceId` from the Media Manager config.
- **`tinymce-custom-file-picker.js`**:
    - Listen for the `instanceId` message and inject it into the parent form's `mle_instance_ids[]` array.
    - Improve `baseId` detection using the `textarea`'s attributes or ID.

#### PHP
- **`Mlbrgn\MediaLibraryExtensions\Support\InstanceManager`**:
    - Remove hashing logic; `getInstanceId` will simply return the input string.
- **`Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent`**:
    - Change `$id` from optional to required (non-nullable).
    - Remove random `Str::ulid()` generation.
- **`Mlbrgn\MediaLibraryExtensions\Services\TemporaryUploadPromoter`**:
    - Implement "wildcard" promotion fallback when no `instance_id` is provided in the request.

### Risks
- **ID Collisions**: Multiple components without explicit IDs might generate the same deterministic ID. Developers will be encouraged to provide explicit IDs for multiple instances of the same component type.

# Testing

### Validation Approach
Verification will be done by running existing PHPUnit tests and adding new tests if needed to cover the changed logic.

### Key Scenarios
- **ID Stability**: Verify that `InstanceManager` and `BaseComponent` produce the same ID across multiple calls/renders.
- **TinyMCE Promotion**: Verify that uploads from TinyMCE are correctly promoted when the form is submitted, specifically checking if the `mle_instance_ids[]` are correctly injected and processed.
- **Wildcard Fallback**: Verify that uploads are promoted based on `client_token` when `instance_id` is missing.

### Regression Testing
- Run all component tests (`tests/Feature/Components/*`)
- Run all promoter tests (`tests/Feature/Services/TemporaryUploadPromoterTest.php`, `tests/Feature/Services/MediaManagerUploadPromotionTest.php`)
- Run TinyMCE specific tests (`tests/Feature/Components/MediaManagerTinymceTest.php`)

# Delivery Steps

### ✓ Step 1: Simplify InstanceManager and BaseComponent IDs
Simplify the ID system by removing randomness and ensuring stability.
- Update `InstanceManager` to remove hashing logic, making `instanceId` 1:1 with the input.
- Update `BaseComponent` (and child classes) to make `$id` a required parameter and remove random fallback logic.
- **Validation**: Run `tests/Feature/Support/InstanceManagerTest.php` and `tests/Feature/Components/BaseComponentTest.php`.

### ✓ Step 2: Fix TinyMCE JavaScript communication and auto-registration
Fix the communication between the TinyMCE media manager iframe and the parent form.
- Uncomment and update the logic in `tinymce-custom-file-picker-iframe.js` to extract and send `instanceId`.
- Update `tinymce-custom-file-picker.js` to listen for the `instanceId` and automatically inject `<input name="mle_instance_ids[]">` in the parent form.
- Improve `baseId` detection to use the `textarea`'s identity.
- **Validation**: Run `tests/Feature/Components/MediaManagerTinymceTest.php`.

### ✓ Step 3: Update TinyMCE Wrapper and ID fallbacks
Ensure that all TinyMCE related components use stable IDs.
- Update `media-manager-tinymce-wrapper.blade.php` to handle missing IDs by using a strictly defined fallback (like field name).
- **Validation**: Run relevant TinyMCE component tests.

### ✓ Step 4: Enhance Temporary Upload Promoter with Wildcard Fallback
Make the backend promotion more robust and simple by adding a fallback mechanism.
- Update `TemporaryUploadPromoter::promoteAllForModel` to support "wildcard" promotion for the current `client_token` as a fallback.
- **Validation**: Run `tests/Feature/Services/TemporaryUploadPromoterTest.php` and `tests/Feature/Services/MediaManagerUploadPromotionTest.php`.

### ✓ Step 5: Cleanup and Final Verification
Final check of the system and cleanup of any remaining orphaned temporary files.
- Ensure the `medialibrary-extensions:remove-expired-temporary-uploads` command works correctly.
- **Validation**: Run all feature tests to ensure overall system stability.