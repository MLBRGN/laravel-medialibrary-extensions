# Simplified ID Architecture Plan (Final – No Fallbacks)

This document outlines the detailed plan to simplify the component identity system within `mlbrgn/laravel-medialibrary-extensions`.

## 1. Goal
Consolidate all identity logic around a single, authoritative **Base ID** (`$id`). All historical identifiers and compatibility layers are removed. No fallbacks, no aliases.

## 2. Final State (Authoritative Base ID)

| Concept | Implementation |
| :--- | :--- |
| **Logical Identity** | `$id` (constructor) — The single authoritative **Base ID** |
| **DOM Identity** | `getDomId()`, `domIdSuffix()` (derived from `$id`) |
| **Upload Scope** | `$instanceId` (automatically derived from `$id`) |
| **Parent Reference** | `$id` (container passes its own `$id` to children) |
| **XHR Payload** | `base_id` only |

## 3. Implementation Phases

### Phase 1: `BaseComponent` Refactor (Authoritative Behavior)
- `BaseComponent` treats `$id` as the single source of truth.
- Standardize `$instanceId` generation in the constructor: `$this->instanceId = InstanceManager::getInstanceId($this->id)`.
- `getDomId()` remains the sole method to compute HTML `id` attributes (derived from `$id`).
- Remove any conditional logic that attempts to resolve IDs from legacy names.

### Phase 2: Component Property Cleanup (No Legacy)
- Remove redundant properties entirely (do not deprecate): `mediaManagerId`, `mediaManagerDomId`, and variations from all components (e.g., `MediaPreviews`, `MediaModal`, `UploadForm`, etc.).
- Constructors of sub-components must accept only `$id` for identity; reject any legacy parameters.
- Container components (e.g., `MediaManager`) must pass their own `$id` as the child’s `$id`.
- Delete helper accessors and mutators related to legacy IDs.

### Phase 3: Blade & JavaScript Consolidation (Single Naming)
- Blade must not reference legacy attributes; use `$id` exclusively.
- Root DOM element must include `data-base-id="{{ $id }}"` for JS discovery.
- JavaScript:
  - Config and runtime must read identity from `data-base-id` (or `config.id`).
  - All XHRs must send `base_id`.
  - Remove any code paths, variables, or data attributes related to legacy identifiers.

### Phase 4: Backend & Response Handling (Strict Input/Output)
- Requests only accept `base_id`.
- Derive `instance_id` server-side from `base_id` (no client-provided instance IDs).
- Responses must include `base_id` where the frontend needs to target updates.
- Remove all legacy request keys from validation rules, DTOs, actions, and transformers.

## 4. Verification Strategy
- Unit: `BaseComponent` derives `instanceId` and `domId` exclusively from `$id`.
- Feature: Temporary uploads are scoped to `instanceId` derived from `base_id`.
- HTTP Validation: Requests must provide `base_id`; unknown keys are not part of the public contract.
- Browser/XHR: Deleting media, editing images, and uploads refresh the correct UI using `base_id`.

## 5. Finalization: Removal of Fallbacks and Legacy

The Base ID flow is the only supported approach across PHP, Blade, and JavaScript. All fallbacks and legacy aliases are removed.

- Accepted identifiers: `id` (PHP/Blade) and `base_id` (XHR payloads). Nothing else.
- Codebase removals checklist:
  - Delete legacy properties and accessors in PHP components.
  - Remove legacy request keys from form requests, actions, and controllers.
  - Drop legacy Blade attributes and data attributes.
  - Purge JS branches and variables that read/write legacy keys.
  - Update tests and snapshots to reflect `base_id`-only behavior and absence of legacy attributes.

### Snapshot Tests
If snapshot tests fail due to expected structural or attribute updates (e.g., removal of legacy attributes), refresh them using:

```
composer test -- --update-snapshots
```

Only update snapshots after verifying that the diffs are intentional.
