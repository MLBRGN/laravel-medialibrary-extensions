---
sessionId: session-260923-234404-kjl2
status: implemented
---

# [Implemented] Requirements

### Overview & Goals
The goal is to simplify the media count validation logic by eliminating duplication between `MinMediaCount`, `MaxMediaCount`, and `MaxTemporaryUploadCount`. We will consolidate the counting logic into the `MediaCounter` service and introduce a shared base class for the validation rules.

### Scope
- **Consolidation**: Move core counting logic to `MediaCounter` service and use it consistently across rules AND actions.
- **Abstraction**: Create a base class for all count-based media validation rules.
- **Unified Rule**: Introduce a single `MediaCount` rule that can handle minimum, maximum, and exact counts with a fluent API.
- **Action Refactoring**: Update all `Store...Action` classes to use the unified counting API instead of manual calculations or direct model queries.
- **Redundancy Removal**: Eliminate duplicated logic in `MinMediaCount`, `MaxMediaCount`, and `MaxTemporaryUploadCount`.
- **Form Submission Clarity**: Explain the role of `mle_instance_map` in form submissions to ensure multi-manager support works correctly.

### Combined Rule vs. Separate Rules
**Should you combine them? Yes.**
Combining `MinMediaCount` and `MaxMediaCount` into a single `MediaCount` rule is recommended because:
- **Fluent API**: It allows for a more readable syntax: `new MediaCount($model, ['images'])->min(1)->max(5)`.
- **DRY**: It further reduces boilerplate when both limits are needed.
- **Maintenance**: Changes to the counting logic only need to be reflected in one place.

We will keep the existing rules as thin wrappers to maintain backward compatibility and for cases where only a single limit is needed and a simple class name is preferred.

# Technical Design

### Current Implementation
The current rules (`MinMediaCount`, `MaxMediaCount`, `MaxTemporaryUploadCount`) manually calculate the media count by summing:
1. Permanent media on the model (if present).
2. Items in the current `$value` (direct uploads).
3. Temporary uploads in the database.

This logic is duplicated across all three rules, leading to maintenance challenges.

### Proposed Changes

#### 1. `MediaCounter` Service Enhancements
Update `MediaCounter.php` to provide a robust high-level API for counting all media associated with a context. This method will be used by both validation rules and action classes.

```php
public function getEffectiveMediaCount(
    array $collections,
    ResolvedModel|HasMedia|string|null $model = null,
    ?string $instanceId = null,
    ?string $clientToken = null,
    ?string $dataSource = 'default',
    mixed $value = null,
    bool $ignoreClientToken = false
): int {
    $count = 0;
    
    // 1. Resolve model if needed
    $resolvedModel = $model instanceof ResolvedModel 
        ? $model 
        : app(MediaModelResolver::class)->resolveModelReference($model, $dataSource);

    // 2. Count permanent media
    if ($resolvedModel->model) {
        $count += $this->countModelMediaInCollections($resolvedModel->model, $collections, $dataSource);
    }
    
    // 3. Count temporary uploads
    if ($instanceId) {
        // Some actions (like StoreMultipleTemporaryAction) require ignoring client_token 
        // to enforce global capacity limits per instance.
        $token = $ignoreClientToken ? null : $clientToken;
        $count += $this->countTemporaryUploadsInCollections($collections, $instanceId, $token, $dataSource);
    }

    // 4. Add count from $value (for validation of direct uploads)
    if (is_array($value)) {
        $count += count($value);
    } elseif (filled($value) && ! is_numeric($value)) {
        // Non-numeric filled value usually represents a single file upload in Laravel
        $count += 1;
    }
    
    return $count;
}
```

#### 2. `mle_instance_map` and Form Submission
The `media-manager` and `media-lab` components automatically include `mle_instance_map` in form submissions.
- **Purpose**: This maps an attribute name (e.g., `blog-main`) to its specific `instance_id`.
- **Multi-Manager Support**: This allows the server to correctly identify which temporary uploads belong to which form field when multiple media managers are present on the same page.
- **Validation**: The validation rules will use this map to resolve the correct `instance_id` for the attribute being validated.
- **Field Values**: The field itself (e.g., `blog-main`) often contains the current count (`0`, `1`, etc.) as a numeric value. The validation rule correctly identifies this as a "client-side hint" and performs its own authoritative count from the database for security.

#### 3. `AbstractMediaCountRule`
A new base class `src/Rules/AbstractMediaCountRule.php` will handle:
- Property storage (`$model`, `$collections`, etc.).
- Shared logic for resolving `instanceId` and `clientToken` from the request if not explicitly provided.
- Nuanced message generation based on limits and the `multiple` flag.

#### 4. Unified `MediaCount` Rule
A new rule `src/Rules/MediaCount.php` with fluent methods:
- `min(int $value)`
- `max(int $value)`
- `exactly(int $value)`
- `multiple(bool $value)`

#### 5. Action Refactoring
Refactor the following actions to use `MediaCounter::getEffectiveMediaCount()`:
- `StoreMultiplePermanentAction`
- `StoreMultipleTemporaryAction`
- `StoreSinglePermanentAction`
- `StoreSingleTemporaryAction`
- `StoreYouTubeVideoPermanentAction`
- `StoreYouTubeVideoTemporaryAction`

This will fulfill several "TODO" items found in these files and ensure capacity limits are checked consistently.

### File Structure
- `src/Services/MediaCounter.php` (Modified)
- `src/Rules/AbstractMediaCountRule.php` (New)
- `src/Rules/MediaCount.php` (New)
- `src/Rules/MinMediaCount.php` (Refactored to extend `AbstractMediaCountRule`)
- `src/Rules/MaxMediaCount.php` (Refactored to extend `AbstractMediaCountRule`)
- `src/Rules/MaxTemporaryUploadCount.php` (Refactored to extend `AbstractMediaCountRule`)
- `src/Actions/StoreMultiplePermanentAction.php` (Refactored)
- `src/Actions/StoreMultipleTemporaryAction.php` (Refactored)
- `src/Actions/StoreSinglePermanentAction.php` (Refactored)
- `src/Actions/StoreSingleTemporaryAction.php` (Refactored)
- `src/Actions/StoreYouTubeVideoPermanentAction.php` (Refactored)
- `src/Actions/StoreYouTubeVideoTemporaryAction.php` (Refactored)

# Testing

### Validation Approach
- Verify that `MediaCount` correctly identifies violations for min, max, and exact limits.
- Ensure `instanceId` resolution works correctly from the request (using `mle_instance_map`).
- Confirm that existing rules (`MinMediaCount`, etc.) still function as expected after refactoring.

### Key Scenarios
1. **New Model**: Count should only include temporary uploads and current `$value`.
2. **Existing Model**: Count should sum permanent media, temporary uploads, and current `$value`.
3. **Multiple Collections**: Count should aggregate across all specified collections.
4. **Data Sources**: Ensure counts are performed on the correct database connection.

# Delivery Steps

###   Step 1: Refactor MediaCounter service logic
Implement `MediaCounter::getEffectiveMediaCount` to correctly sum permanent media, temporary uploads, and direct uploads.
- Integrate `MediaModelResolver` to handle different model reference types.
- Ensure it properly aggregates counts from all sources.
- Update `ChecksMediaLimits` trait to use the new service logic.

###   Step 2: Create AbstractMediaCountRule and unified MediaCount rule
Introduce the shared rule architecture.
- Create `AbstractMediaCountRule` to consolidate shared properties and request resolution logic.
- Implement the fluent `MediaCount` rule supporting `min`, `max`, `exactly`, and `multiple` configurations.

###   Step 3: Refactor existing validation rules
Update `MinMediaCount`, `MaxMediaCount`, and `MaxTemporaryUploadCount` to extend `AbstractMediaCountRule`.
- Remove redundant properties and logic from these classes.
- Update their `validate` methods to use `getEffectiveCount` from the base class.

###   Step 4: Refactor Action classes to use unified counting
Update all `Store...Action` classes to use `MediaCounter::getEffectiveMediaCount()`.
- Clean up `StoreMultiplePermanentAction`, `StoreMultipleTemporaryAction`, etc.
- Remove direct database queries and manual sums from these actions.
- Ensure capacity limits are enforced consistently across the package.