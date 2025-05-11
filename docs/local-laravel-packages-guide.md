
# Working with Local Laravel Packages Using Composer

**Last Modified:** February 23th, 2023

Learn how to create symbolic links for your local packages, set up Composer to automatically read these packages, and require them in your Laravel project. This guide offers step-by-step instructions and best practices for Laravel package management, ideal for developers aiming to streamline their workflow and increase productivity.

---

## Step-by-Step Guide

### 1. Create a New Directory for Your Package

Create a new directory for your package inside a `packages/vendor/my-package` directory in the root of your Laravel application.

For example, if your package is named `my-package` and your vendor name is `my-vendor`, create a directory like so:

```
packages/my-vendor/my-package
```

---

### 2. Develop Your Package

Inside the `my-package` directory, start developing your package. A typical Laravel package structure includes:

- a `src` directory  
- a `composer.json` file  
- and optionally: `routes`, `views`, `migrations`, etc.

👉 You can use this excellent Laravel package boilerplate from BeyondCode:  
[https://laravelpackageboilerplate.com/](https://laravelpackageboilerplate.com/)

---

### 3. Update Your Root `composer.json` File

In your Laravel app’s root `composer.json`, add a path repository pointing to your local package. This tells Composer where to find the package.

```json
"repositories": [
  {
    "type": "path",
    "url": "./packages/vendor/my-package",
    "options": {
      "symlink": true
    }
  }
]
```

> ✅ Note: `"./packages/vendor/my-package"` is a relative path from your Laravel app root.

---

### 4. Require Your Package

In the `"require"` section of your Laravel app’s `composer.json`, add your package:

```json
"require": {
  "vendor/my-package": "*"
}
```

> Replace `"vendor/my-package"` with the name defined in your package's `composer.json`.

---

### 5. Install Your Package

Run the following in your Laravel app root:

```bash
composer update
```

This will install your package into the `vendor` directory, creating a symbolic link to your local package.

---

### 6. Use Your Package

Now you can start using your package in your Laravel application.

Since you're using a symbolic link, any changes made in `packages/my-vendor/my-package` will be reflected immediately.

> 🛠️ Be sure to use service providers to register your package's resources: routes, views, migrations, etc.

---

## Bonus: Using a Wildcard for Multiple Packages

If you’re developing multiple packages and want Composer to symlink all of them from your `packages` directory, you can use a wildcard:

```json
"repositories": [
  {
    "type": "path",
    "url": "./packages/*",
    "options": {
      "symlink": true
    }
  }
]
```

---

## Final Notes

Working with local Laravel packages doesn't have to be complex. By setting up Composer with path repositories and symbolic links, you can streamline your development workflow.

Whether you're working on personal packages or collaborating with a team, this setup helps you manage local packages more efficiently.

---
