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
