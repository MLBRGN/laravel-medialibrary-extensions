# Media Resolution Refactor Proposal

## Goal

Simplify the package by giving every class a single responsibility.

The current implementation mixes several concepts:

- resolving a model class from a morph alias
- validating model classes
- instantiating models
- loading existing models
- assigning database connections
- determining temporary upload mode

These responsibilities are currently spread across Blade components, Form Requests and MediaService.

The goal is to make **MediaService the single place responsible for model resolution**.

---

## Current status (in progress)

- New MediaService APIs are in place (BC-safe):
  - `resolveModelReference()` (normalizes morph alias / FQCN)
  - `makeModel()` (instantiate with correct connection)
  - `findModel()` (load existing model via normalized reference)
- Legacy APIs still exist and delegate to the new ones (soft-deprecated):
  - `resolveModelOrClassName()` → `resolveModelReference()`
  - `make()` → `makeModel()`
  - `resolveModelById()` → `findModel()`
- Blade components:
  - `MediaManager` accepts `modelReference` (preferred) with fallback to `modelOrClassName` (BC).
  - `MediaManagerSingle` and `MediaManagerTinymce` also accept `modelReference` with fallback (BC).
- Security/authorization behavior unchanged: 403 for ownership/authorization failures, 422 for validation.

What remains (tracked below) keeps tests green throughout migration.

---

# 1. Rename `modelOrClassName` to `modelReference`

The public API currently exposes:

```php
public Model|string $modelOrClassName;
```

This name describes the PHP type instead of the concept.

A better name is:

```php
public Model|string $modelReference;
```

because both values represent the same thing:

- an existing model
- or a reference to the model that will eventually own the uploaded media.

Example:

```blade
<x-media-manager :model-reference="$blog" />

<x-media-manager :model-reference="App\Models\Blog::class" />
```

This makes the public API easier to understand without exposing implementation details.

---

# 2. Move all model resolution into MediaService

Currently model resolution is split across several places.

MediaManagerRequest currently:

- resolves morph aliases
- validates model classes
- resolves models
- catches lookup exceptions

MediaService also:

- creates models
- resolves models
- validates model classes

These responsibilities should live in one place.

---

# 3. MediaManagerRequest should become simple

The Request should only be responsible for:

- reading request input
- authorization
- validation

It should no longer know about:

- Relation::getMorphedModel()
- class_exists()
- HasMediaExtended validation
- database connections
- model lookup exceptions

Instead it should simply ask MediaService.

Example:

```php
$model = $mediaService->resolveRequestModel(...);
```

or

```php
$model = $mediaService->makeRequestModel(...);
```

---

## FormRequest changes (summary)

Objective: keep FormRequests focused on input, authorization, and validation; delegate model resolution to MediaService while preserving existing HTTP status codes and messages.

Implemented:

- `MediaManagerRequest`
  - `resolveModelClass()` now delegates to `MediaService::resolveModelReference()` and returns the normalized model type.
  - `mediaModel()` and `resolveModel()` delegate instantiation and loading to `MediaService::makeModel()` / `findModel()` and swallow lookup exceptions to allow `authorize()` to return `false` (403) rather than throwing.
  - Authorization helpers (`authorizeMediaUpload|Edit|Delete`) continue to return 403 on failures; validation remains 422.

Planned/ongoing:

- Child requests (destroy, update/replace, restore-original, set-as-first, and temporary-upload variants) should not perform any direct class resolution; where present, they will call the parent helpers that already delegate to `MediaService`.
- No change to translation keys or response payloads; actions keep using 403 with `medialibrary-extensions::messages.not_authorized` for ownership/authorization mismatches.

Verification:

- Pest suites (Actions, MediaManager, Security) are kept green after each step. Any failures arising from moved exceptions are addressed by keeping where HTTP codes are decided unchanged (FormRequest vs Action).

---

## Delegation matrix (Requests → MediaService)

Legend: ✓ = already delegates; → = planned delegation

- MediaManagerRequest
  - `resolveModelClass()` → delegates to `MediaService::resolveModelReference()` ✓
  - `mediaModel()` → delegates to `MediaService::makeModel()` ✓
  - `resolveModel()` → delegates to `MediaService::findModel()` and swallows lookup exceptions ✓
  - `authorizeMediaAction()` → uses `resolveModel()` result, no direct class logic ✓

- DestroyRequest
  - Uses parent `authorizeMediaDelete()` and resolution helpers ✓

- StoreUpdatedMediaRequest
  - Uses parent `authorizeMediaEdit()` and resolution helpers ✓

- RestoreOriginalMediumRequest
  - Uses parent `authorizeMediaEdit()` and resolution helpers ✓

- SetMediumAsFirstRequest
  - Uses parent helpers for authorization and resolution ✓

- Temporary upload requests (DestroyTemporaryUploadRequest, SetTemporaryUploadAsFirstRequest, etc.)
  - Authorization: scoped via temporary‑mode rules (no model lookup) ✓
  - Resolution of temporary uploads: via `MediaService` scoped finders ✓

If any child request contains residual direct class checks (e.g., `class_exists`, morph map calls), it will be removed and delegated to the parent helpers, which already call `MediaService`.

---

## Deprecation timeline

- Now (current minor):
  - Blade: `model-reference` supported and preferred; `model-or-class-name` remains fully supported.
  - Service: `resolveModelReference()`, `makeModel()`, `findModel()` are primary; legacy names exist and proxy with `@deprecated` PHPDoc.

- Next minor:
  - Emit deprecation notices (logs) when `model-or-class-name` is used without `model-reference`.
  - Mark legacy service methods as deprecated in the docs and changelog.

- Next major (breaking):
  - Remove `model-or-class-name` from component public APIs.
  - Remove legacy service method names (`resolveModelOrClassName`, `make`, `resolveModelById`).
  - Public docs and upgrade guide will provide straightforward replacements.

---

## API diff (old → new) with examples

This section shows common call sites and their BC‑safe replacements. Legacy APIs remain available for now and proxy to the new implementations.

### Service methods

| Old | New | Notes |
|-----|-----|------|
| `resolveModelOrClassName($ref)` | `resolveModelReference($ref, $dataSource)` | New returns a normalized `ResolvedModel` value object; callers typically use `$resolved->modelType`. |
| `make($ref, $dataSource)` | `makeModel($ref, $dataSource)` | Single place that instantiates and assigns the correct connection. |
| `resolveModelById($ref, $id, $dataSource)` | `findModel($ref, $id, $dataSource)` | Uses `makeModel()` internally, then performs the lookup. |

Examples:

```php
// Before
$class = $mediaService->resolveModelOrClassName($input);
$model = $mediaService->make($class, $dataSource);
$found = $mediaService->resolveModelById($class, $id, $dataSource);

// After (BC-safe)
$resolved = $mediaService->resolveModelReference($input, $dataSource);
$model = $mediaService->makeModel($resolved->modelType, $dataSource);
$found = $mediaService->findModel($resolved->modelType, $id, $dataSource);
```

### FormRequests

```php
// Before (request mixed resolution responsibilities)
protected function resolveModelClass(): ?string {
    // Relation::getMorphedModel(), class_exists(), interface checks...
}
protected function resolveModel(): ?HasMediaExtended {
    // new $class, setConnection, query -> findOrFail, try/catch
}

// After (delegation to MediaService)
protected function resolveModelClass(): ?string {
    $resolved = app(MediaService::class)->resolveModelReference($this->string('model_type')->toString(), $this->input('data_source', 'default'));
    return $resolved->modelType;
}
protected function resolveModel(): ?HasMediaExtended {
    try {
        return app(MediaService::class)->findModel($this->resolveModelClass(), $this->input('model_id'), $this->input('data_source', 'default'));
    } catch (\Throwable $e) {
        return null; // Let authorize()/validation decide (403/422)
    }
}
```

Authorization behavior is unchanged; `authorize()` continues to return `false` → 403, and validation errors remain 422.

### Actions

```php
// Before
$model = $this->mediaService->resolveModelById($request->input('model_type'), $request->input('model_id'), $request->input('data_source'));

// After
$model = $this->mediaService->findModel($request->input('model_type'), $request->input('model_id'), $request->input('data_source'));
```

Keep ownership checks and 403 `not_authorized` responses as implemented.

### Blade components

```blade
{{-- Before --}}
<x-media-manager :model-or-class-name="$blog" :collections="['image' => 'blog-main']" />
<x-media-manager :model-or-class-name="App\Models\Blog::class" :collections="['image' => 'blog-main']" />

{{-- After (preferred) --}}
<x-media-manager :model-reference="$blog" :collections="['image' => 'blog-main']" />
<x-media-manager :model-reference="App\Models\Blog::class" :collections="['image' => 'blog-main']" />

{{-- BC still supported: model-or-class-name continues to work in the current minor --}}
```

# 4. Clarify MediaService responsibilities

MediaService currently mixes several unrelated responsibilities.

Suggested sections:

```
Model Resolution
----------------
resolveModelReference()
makeModel()
findModel()

Media Retrieval
---------------
resolveMedia()

Media Counting
--------------
countMedia()

Utilities
---------
determineCollectionType()
collectionNames()
```

This makes the class much easier to navigate.

---

# 5. Improve method names

Current names:

```
resolveModelOrClassName()
make()
resolveModelById()
```

are difficult to understand because they overlap.

Suggested names:

```
resolveModelReference()
makeModel()
findModel()
```

These describe three distinct operations.

| Method | Responsibility |
|---------|----------------|
| resolveModelReference() | Normalize a model reference into an internal representation |
| makeModel() | Instantiate a model without loading it |
| findModel() | Load an existing model from the database |

---

# 6. Remove duplicated model setup

Both `make()` and `resolveModelById()` currently:

- validate the model class
- instantiate the model
- assign the database connection

This setup should exist only once.

Example:

```
makeModel()
↓

instantiate model
assign connection

↓

return model
```

Then:

```
findModel()

↓

makeModel()

↓

findOrFail()
```

---

# 7. Reduce branching throughout the package

Many places currently check:

```php
if ($modelReference instanceof HasMedia) {
...
}

if (is_string($modelReference)) {
...
}
```

The goal is to centralize these decisions inside MediaService.

Callers should no longer care whether they received:

- a model
- or a model class

They should simply request the operation they need.

---

# 8. Long-term goal

The package should eventually have a very small public API for model handling.

Example:

```
resolveModelReference()

makeModel()

findModel()

resolveMedia()

countMedia()
```

Everything else should become implementation details.

---

# Benefits

- Smaller Request classes
- Cleaner Blade components
- One place responsible for model resolution
- Less duplicated validation
- Less duplicated connection handling
- More descriptive method names
- Easier testing
- Easier maintenance
- Clear separation of responsibilities

---

## Backward compatibility notes

- Blade attribute naming: use `model-reference` (preferred). `model-or-class-name` remains supported for now and will be deprecated in a future major.
- Public API deprecations are soft and proxied; no behavior changes expected for existing consumers.
- Status codes policy retained: 403 for authorization/ownership failures, 404 for true missing resources when desired, 422 for validation errors.
