### Media Promotion Fix for Multiple Instances

#### Overview
This document summarizes the investigation and fix for a bug where temporary uploads were not correctly promoted to permanent media when multiple media manager components were present on a single form.

#### The Problem
1.  **Multiple Media Managers**: When a form contains multiple `MediaManager` components (e.g., one for images and one for videos), each has its own `instance_id`.
2.  **ID Collision**: Because each component used the same hidden input name `instance_id`, the values would overwrite each other during form submission, or only one would be sent to the server.
3.  **Strict Promotion Logic**: The `TemporaryUploadPromoter` previously only looked for a single `instance_id` string in the request. If it received the ID for the video manager, it would ignore the temporary uploads for the image manager, leaving them orphaned in the database.
4.  **Persistent Side Effects**: Orphaned uploads remained associated with the user's `client_token`. Consequently, the next time the user opened the creation form, these "stranded" images would reappear unexpectedly.

#### The Fix
The fix enables support for multiple `instance_id` values throughout the promotion lifecycle and introduces form isolation to prevent field pollution:

1.  **Frontend Components**:
    *   Updated `MediaManager` components (Bootstrap 5 and Plain themes) to include a hidden input: `<input type="hidden" name="mle_instance_ids[]" value="{{ $instanceId }}">`.
    *   **New**: Named components now also include an `mle_instance_map[name]` field. This specifically maps your form attribute (e.g., `blog-main`) to its specific `instanceId`, allowing for targeted server-side validation.
    *   **Form Isolation**: To prevent internal XHR fields (client tokens, instance IDs) from polluting your parent form's submission, the package now automatically isolates these fields into a separate hidden form using the HTML `form` attribute.

2.  **Backend Promoter**:
    *   Modified `Mlbrgn\MediaLibraryExtensions\Services\TemporaryUploadPromoter::promoteAllForModel` to handle `instanceId` as either a string or an array.
    *   The promoter now uses `whereIn('instance_id', $instanceId)` when an array is provided.
    *   It prioritizes the new `mle_instance_ids` request parameter.

3.  **Model Trait & Validation**:
    *   Updated `Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended` to also prioritize `mle_instance_ids` when determining which uploads to promote during the `saved` event.
    *   **New**: Validation rules (`MediaCount`, `MinMediaCount`, etc.) use the `mle_instance_map` to perform accurate, authoritative counts for specific attributes.

#### Verification
*   **Reproduction Test**: A new test `tests/Feature/WorkplaceMediaPromotionTest.php` was created in the `activerendwerk` project.
*   **Form Isolation Test**: Verified via `tests/Feature/Components/FormIsolationTest.php`, ensuring internal fields are moved and `mle_instance_map` is correctly submitted.
*   **Outcome**: All media from all instances are promoted and no temporary records remain.

#### Files Modified
*   `src/Services/TemporaryUploadPromoter.php`
*   `src/Traits/InteractsWithMediaExtended.php`
*   `resources/views/components/bootstrap-5/media-manager.blade.php`
*   `resources/views/components/plain/media-manager.blade.php`
*   `resources/js/shared/form.js` (Form Isolation logic)
*   `src/Rules/AbstractMediaCountRule.php` (Mapping resolution)

#### Recommendations
*   Ensure that any custom themes or components also adopt the `mle_instance_ids[]` hidden input pattern if they are used in forms alongside other media managers.
*   **Nesting in Forms**: With the introduction of **Form Isolation**, it is now fully safe to nest `MediaManager` components within another `<form>`. The internal upload fields will be automatically redirected to an isolated form context, keeping your main form submission clean.
*   The `TemporaryUploadPromoter` remains backward compatible with the single `instance_id` parameter for simple implementations.
