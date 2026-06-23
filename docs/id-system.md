Here's a documentation-style version that reads as project documentation rather than a discussion.

# Component Identity Architecture

## Overview

Media Library Extensions components use multiple identifiers, each with a distinct responsibility. Separating these identities reduces ambiguity, prevents bugs, and makes component relationships easier to understand.

Historically, the `id` property has been used for both logical component identification and HTML DOM identification. This dual responsibility introduces confusion when IDs are modified or suffixed during component construction.

This document defines a clear identity model and the responsibilities of each identifier.

---

# Identity Types

## Component ID (`id`)

The component ID represents the logical identity of a component instance.

### Example

```blade
<x-mle-media-manager id="gallery" />
```

Result:

```php
id = 'gallery';
```

### Responsibilities

* Identifies a media manager instance.
* Remains stable throughout the component lifecycle.
* Used to derive child component identities.
* Used to derive upload scopes.
* Used for component communication.
* Used in Alpine and JavaScript events.
* Used for parent-child relationships.

### Rules

* Must never be mutated.
* Must never be suffixed.
* Must never be overwritten after construction.
* Must not be used as an HTML-specific identifier.

---

## DOM ID (`domId`)

The DOM ID represents the HTML identity of a rendered element.

### Examples

```html
<div id="gallery">
<form id="gallery-upload">
<div id="gallery-preview">
```

### Responsibilities

* HTML element identification.
* JavaScript selectors.
* Alpine targets.
* Browser DOM interactions.

### Rules

* May be derived from the component ID.
* May be suffixed.
* May differ between related components.
* Must not be used for business logic.

---

## Instance ID (`instanceId`)

The instance ID represents a temporary upload scope.

### Example

```php
instanceId = 'abc123';
```

### Responsibilities

Temporary uploads are scoped by:

```text
clientToken + instanceId
```

Example usage:

```php
TemporaryUpload::where(...)
```

### Rules

* Generated once during construction.
* Derived from the logical component ID.
* Never derived from DOM IDs.
* Never modified after construction.

---

## Client Token (`clientToken`)

The client token represents the browser or client identity.

### Example

```php
clientToken = 'xyz789';
```

### Responsibilities

* Distinguishes different browsers.
* Distinguishes different users.
* Works together with `instanceId` to scope temporary uploads.

### Rules

* Never derived from component IDs.
* Never derived from DOM IDs.
* Remains stable during a session.

---

# Goals

No ambiguity because it makes it difficult to determine whether an identifier should be used for:

* HTML rendering
* Upload scoping
* Event communication
* Parent-child relationships

Internally, responsibilities are separated.

| Property      | Meaning                    | Mutable |
| ------------- | -------------------------- | ------- |
| `id`          | Logical component identity | No      |
| `domId`       | HTML/DOM identity          | Yes     |
| `instanceId`  | Temporary upload scope     | No      |
| `clientToken` | Browser/client scope       | No      |

---

# BaseComponent

```php
abstract class BaseComponent extends Component
{
    public string $id;

    public string $domId;

    public string $instanceId;

    public string $clientToken;

    public function __construct(?string $id = null)
    {
        $this->id = filled($id)
            ? $id
            : (string) Str::ulid();

        $this->domId = $this->id;

        $this->instanceId = InstanceManager::getInstanceId($this->id);

        $this->clientToken = app(ClientContext::class)->get();
    }
}
```

---

# Helper Methods

## Generate a DOM ID

```php
public function getDomId(string $suffix): string
{
    return "{$this->id}-{$suffix}";
}
```

Example:

```php
$this->getDomId('mmm');

// gallery-mmm
```

---

## Set the Current DOM ID

```php
public function setDomId(string $suffix): void
{
    $this->domId = $this->getDomId($suffix);
}
```

Example:

```php
$this->setDomId('mmm');

// gallery-mmm
```

---

## Generate a Child DOM ID

```php
public function childDomId(string $name): string
{
    return "{$this->id}-{$name}";
}
```

Examples:

```php
$this->childDomId('upload-form');
// gallery-upload-form

$this->childDomId('preview');
// gallery-preview
```

---

## Generate Nested DOM IDs

```php
public function nestedDomId(string ...$segments): string
{
    return implode('-', [
        $this->id,
        ...$segments,
    ]);
}
```

Examples:

```php
$this->nestedDomId('preview');
// gallery-preview

$this->nestedDomId('preview', 'image');
// gallery-preview-image

$this->nestedDomId('upload', 'progress');
// gallery-upload-progress
```

---

## Export Identity Context

```php
public function getIdentityContext(): array
{
    return [
        'id' => $this->id,
        'domId' => $this->domId,
        'instanceId' => $this->instanceId,
        'clientToken' => $this->clientToken,
    ];
}
```

Useful for:

* Alpine
* AJAX requests
* Debugging
* Event payloads

---

# Component Examples

## Media Manager

```php
id          = gallery
domId       = gallery-mmm
instanceId  = abc123
clientToken = xyz789
```

Construction:

```php
$this->setDomId('mmm');
```

---

## Upload Form

Blade:

```blade
<x-mle-partial-upload-form
    :id="$id"
    :instance-id="$instanceId"
/>
```

Construction:

```php
parent::__construct($id);

$this->setDomId('upload-form');
```

Result:

```php
id          = gallery
domId       = gallery-upload-form
instanceId  = abc123
clientToken = xyz789
```

---

## Preview Component

Construction:

```php
parent::__construct($id);

$this->setDomId('preview');
```

Result:

```php
id          = gallery
domId       = gallery-preview
instanceId  = abc123
clientToken = xyz789
```

---

# Relationship Between Components

Child components should continue receiving the logical component ID:

```blade
<x-mle-partial-upload-form
    :id="$id"
/>
```

The upload form belongs to:

```text
gallery
```

not:

```text
gallery-mmm
```

If a child component requires its own DOM identifier, it should derive it internally:

```php
$this->domId = "{$this->id}-upload-form";
```

This ensures all related components share the same logical identity while rendering unique DOM elements.

---

# Identity Rules

## id

Represents:

> The logical component identity.

Examples:

```php
gallery
profile-images
product-media
```

Rules:

* Never mutate.
* Never suffix.
* Never overwrite.
* Safe for business logic.
* Safe for routing.
* Safe for events.
* Safe for component relationships.

---

## domId

Represents:

> The HTML element identity.

Examples:

```php
gallery-mmm
gallery-upload
gallery-preview
```

Rules:

* May be suffixed.
* May be generated.
* Intended exclusively for DOM interaction.

---

## instanceId

Represents:

> Temporary upload scope.

Rules:

* Generated once.
* Derived from the logical component identity.
* Never derived from DOM IDs.

---

## clientToken

Represents:

> Browser/client identity.

Rules:

* Used together with `instanceId`.
* Never derived from DOM IDs.

---

# Mental Model

```text
Media Manager
│
├── id = gallery
├── instanceId = abc123
├── clientToken = xyz789
│
├── domId = gallery-mmm
│
├── Upload Form
│   └── domId = gallery-upload-form
│
├── Preview
│   └── domId = gallery-preview
│
└── Progress Bar
    └── domId = gallery-progress
```

All related components share the same logical identity (`id`) and upload scope (`instanceId`), while each rendered element receives a unique DOM identity (`domId`).

This separation makes the component hierarchy easier to reason about and prevents bugs caused by identifiers changing meaning during construction.
