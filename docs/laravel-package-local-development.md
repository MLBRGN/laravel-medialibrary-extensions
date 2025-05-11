# Laravel Package Development with Local Composer Dependencies

*By Ash Allen*  
*Published: October 6, 2022*  
*Source: [Laravel.io](https://laravel.io/articles/laravel-package-development-with-local-composer-dependencies)*

---

## Introduction

Contributing to open-source projects is a rewarding endeavor. To facilitate this, I maintain a "playground" Laravel project—a fresh installation with minimal setup—to test changes without affecting vendor files directly. This setup allows for local package development, enabling experimentation and contributions via pull requests.

---

## Local Package Development

Assuming a fresh Laravel installation, let's explore installing a fork of my [Short URL package](https://github.com/ash-jc-allen/short-url) (`ashallendesign/short-url`) as a local package. If unfamiliar with forking, refer to [GitHub's guide on forking a repository](https://docs.github.com/en/get-started/quickstart/fork-a-repo).

To inform Composer of local packages, add the following to your project's `composer.json`:

```json
"repositories": {
    "local": {
        "type": "path",
        "url": "./packages/*",
        "options": {
            "symlink": true
        }
    }
}
```

This configuration directs Composer to check the `packages` directory for dependencies before fetching them from external sources. Adjust the `url` path if your packages reside elsewhere.

After updating `composer.json`, it might resemble:

```json
{
    "name": "laravel/laravel",
    "type": "project",
    "description": "The Laravel Framework.",
    "keywords": ["framework", "laravel"],
    "license": "MIT",
    "require": {
        "php": "^8.0.2",
        "guzzlehttp/guzzle": "^7.2",
        "laravel/framework": "^9.11",
        "laravel/sanctum": "^2.14.1",
        "laravel/tinker": "^2.7"
    },
    "require-dev": {
        "fakerphp/faker": "^1.9.1",
        "laravel/sail": "^1.0.1",
        "mockery/mockery": "^1.4.4",
        "nunomaduro/collision": "^6.1",
        "phpunit/phpunit": "^9.5.10",
        "spatie/laravel-ignition": "^1.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "repositories": {
        "local": {
            "type": "path",
            "url": "./packages/*",
            "options": {
                "symlink": true
            }
        }
    }
}
```

Next, clone your package into the `packages` directory:

```bash
git clone https://github.com/ash-jc-allen/short-url.git packages/short-url
```

Alternatively, using GitHub CLI:

```bash
gh repo clone ash-jc-allen/short-url packages/short-url
```

Now, install the package via Composer:

```bash
composer require ashallendesign/short-url
```

This command creates a symlink in the `vendor` directory pointing to your local package, allowing for seamless development.

---

## Using the CLI

Switching between local and remote package versions can be streamlined using Zsh functions. Add the following to your `~/.zshrc`:

```bash
function composerLocal() {
    URL="${2:-./packages/${1}}"
    composer config repositories."$1" '{"type": "path", "url": "'"${URL}"'", "options": {"symlink": true}}' --file composer.json
}

function composerRemote() {
    composer config repositories."$1" --unset
}

function composerVcs() {
    composer config repositories."$1" '{"type": "vcs", "url": "'"${2}"'"}' --file composer.json
}
```

Reload your terminal or run:

```bash
source ~/.zshrc
```

These functions allow you to:

- **`composerLocal`**: Use a local Composer dependency.
- **`composerRemote`**: Revert to the remote package version.
- **`composerVcs`**: Use a package from a VCS repository.

For example:

```bash
composerLocal short-url
```

This command sets the `short-url` package to use the local version in `./packages/short-url`.

---

## Conclusion

This guide provides a straightforward approach to developing and testing Laravel packages locally. By configuring Composer and utilizing simple CLI functions, you can efficiently contribute to open-source projects or develop your own packages with ease.

---

*Original article by [Ash Allen](https://laravel.io/articles/laravel-package-development-with-local-composer-dependencies)*