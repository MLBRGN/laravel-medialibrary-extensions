# Component Identity Architecture

## Overview

Media Library Extensions components use multiple identifiers, each with a distinct responsibility. Separating these identities reduces ambiguity, prevents bugs, and makes component hierarchy easier to understand.

Historically, the `id` property was used for both logical component identification and HTML DOM identification. This dual responsibility introduced confusion.

This document defines a clear identity model and the responsibilities of each identifier.

---

# Identity Types

## Component ID (`id`)

The component ID represents the logical identity of a component instance.

### Example

```blade
<x-mle-media-manager id="gallery" />
```

### Responsibilities

* Identifies a media manager instance.
* Remains stable throughout the component lifecycle.
* Used to derive child component identities.
* Used to derive upload scopes.
* Used for component communication.
* Used for parent-child relationships.

### Rules

* Must never be mutated.
* Must never be suffixed.
* Must never be overwritten after construction.
* Must not be used as an HTML-specific identifier.

---

## DOM ID (`$getDomId()`)

The DOM ID represents the HTML identity of a rendered element and is retrieved via the `$getDomId()` method.

### Examples

```html
<div id="{{ $getDomId() }}">
<form id="{{ $getDomId() }}-upload">
<div id="{{ $getDomId() }}-preview">
```

### Responsibilities

* HTML element identification.
* JavaScript selectors.
* Alpine targets.
* Browser DOM interactions.

### Rules

* Derived from the component ID.
* May be suffixed (via `domIdSuffix()` in the component class).
* May differ between related components.
* Must not be used for business logic.

---

## Instance ID (`instanceId`)

The instance ID represents a temporary upload scope.

### Responsibilities

Temporary uploads are scoped by `clientToken + instanceId`.

### Rules

* Generated once during construction.
* Derived from the logical component ID.
* Never derived from DOM IDs.
* Never modified after construction.

---

## Client Token (`clientToken`)

The client token represents the browser or client identity.

### Responsibilities

* Distinguishes different browsers/clients.
* Works with `instanceId` to scope temporary uploads.

---

# Goals

Internally, responsibilities are separated to avoid ambiguity.

| Property      | Meaning                    | Mutable |
| ------------- | -------------------------- | ------- |
| `id`          | Logical component identity | No      |
| `$getDomId()` | HTML/DOM identity          | Yes     |
| `instanceId`  | Temporary upload scope     | No      |
| `clientToken` | Browser/client scope       | No      |

---

# BaseComponent

```php
abstract class BaseComponent extends Component
{
    public string $id;
    public string $instanceId;
    public string $clientToken;

    public function __construct(?string $id = null)
    {
        $this->id = filled($id) ? $id : (string) Str::ulid();
        $this->instanceId = InstanceManager::getInstanceId($this->id);
        $this->clientToken = app(ClientContext::class)->get();
    }

    public function getDomId(): string
    {
         // returns id + domIdSuffix()
    }
}
```

---

# Helper Methods

## Generate a DOM ID

Blade templates use the `$getDomId()` method to retrieve the HTML identity.

```blade
<div id="{{ $getDomId() }}">
```

In the component class, you can call `$this->getDomId()`:

```php
$domId = $this->getDomId();
```

Example with suffix (managed via `domIdSuffix()` in child components):

```php
// If domIdSuffix() returns 'mmm'
$this->getDomId(); // gallery-mmm
```

---

# Component Examples

## Media Manager

```php
id          = gallery
instanceId  = abc123
clientToken = xyz789
getDomId()  = gallery-mmm (if domIdSuffix is 'mmm')
```

## Upload Form

Result:

```php
id          = gallery
instanceId  = abc123
clientToken = xyz789
getDomId()  = gallery-upload-form (if domIdSuffix is 'upload-form')
```

---

# Relationship Between Components

Child components should continue receiving the logical component ID:

```blade
<x-mle-partial-upload-form :id="$id" />
```

If a child component requires its own DOM identifier, it should define it via `domIdSuffix()`:

```php
protected function domIdSuffix(): string
{
    return 'upload-form';
}
```

This ensures all related components share the same logical identity while rendering unique DOM elements via `$getDomId()`.

---

# Identity Rules Summary

## id
* Logical component identity.
* Never mutate/suffix.
* Safe for business logic and events.

## DOM ID
* HTML element identity, accessed via `$getDomId()`.
* May be suffixed.
* Intended exclusively for DOM interaction.

## instanceId
* Temporary upload scope.
* Derived from `id`.

## clientToken
* Browser/client identity.

---

# Mental Model

```text
Media Manager
│
├── id = gallery
├── instanceId = abc123
├── clientToken = xyz789
│
├── domId = gallery-mmm (via $getDomId())
│
├── Upload Form
│   └── domId = gallery-upload-form (via $getDomId())
│
├── Preview
│   └── domId = gallery-preview (via $getDomId())
│
└── Progress Bar
    └── domId = gallery-progress (via $getDomId())
```

All related components share the same logical identity (`id`) and upload scope (`instanceId`), while each rendered element receives a unique DOM identity via `$getDomId()`.
