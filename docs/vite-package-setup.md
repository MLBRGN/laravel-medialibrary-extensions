# 📦 Vite Integration for `spatie-media-library-extensions` Package

This guide explains how to set up and load Vite assets (CSS and JS) from the `spatie-media-library-extensions` package inside a Laravel application. Written by ChatGPT.

TODO: Proof-read
---

## 📁 Folder Structure

Your project should look like this:

```
main-laravel-project/
├── app/
├── packages/
│   └── mlbrgn/
│       └── spatie-media-library-extensions/
│           ├── resources/
│           │   ├── css/app.css
│           │   └── js/app.js
│           ├── public/
│           │   ├── [built assets go here]
│           │   └── [will contain .hot during dev]
│           ├── vite.config.js
│           └── package.json
└── public/
    └── vendor/
        └── spatie-media-library-extensions/ → symlink to package's `public/` directory
```

---

## ⚙️ 1. Create Vite Config in Your Package

In `packages/mlbrgn/spatie-media-library-extensions/vite.config.js`:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            publicDirectory: 'public',
            buildDirectory: 'public',
        }),
    ],
    resolve: {
        alias: {
            '~': path.resolve(__dirname, 'resources/js'),
        },
    },
    server: {
        origin: 'http://localhost:5173',
    },
});
```

---

## 📦 2. Install NPM Dependencies

```bash
cd packages/mlbrgn/spatie-media-library-extensions
npm install
```

---

## 🚀 3. Start the Vite Dev Server

```bash
npm run dev
```

This will create a `.hot` file inside the `public` folder.

---

## 🔗 4. Symlink the Public Directory

Run this command from the **root of your Laravel app** to create the symlink:

```bash
ln -s ../../packages/mlbrgn/spatie-media-library-extensions/public public/vendor/spatie-media-library-extensions
```

Or use this script:

```bash
#!/bin/bash

LINK_TARGET="packages/mlbrgn/spatie-media-library-extensions/public"
LINK_NAME="public/vendor/spatie-media-library-extensions"

if [ -L "$LINK_NAME" ] || [ -e "$LINK_NAME" ]; then
    echo "Removing existing link or directory..."
    rm -rf "$LINK_NAME"
fi

echo "Creating symlink..."
ln -s "../../$LINK_TARGET" "$LINK_NAME"
echo "Symlink created: $LINK_NAME → $LINK_TARGET"
```

Save it as `link-package-assets.sh`, make it executable, and run it with:

```bash
chmod +x link-package-assets.sh
./link-package-assets.sh
```

---

## 🧩 5. Add Vite Call in Blade

In your `resources/views/layouts/app.blade.php` or similar:

```blade
{{ Vite::useHotFile('vendor/spatie-media-library-extensions.hot')
    ->useBuildDirectory('vendor/spatie-media-library-extensions')
    ->withEntryPoints([
        'resources/css/app.css',
        'resources/js/app.js',
    ]) }}
```

---

## 🛠️ 6. Production Build

To compile assets for production:

```bash
cd packages/mlbrgn/spatie-media-library-extensions
npm run build
```

This will output compiled files to `public/`, which Laravel will use when no `.hot` file exists.

---

## ✅ Done!

Now your Laravel app will load your package's assets correctly:

- During development via the Vite dev server
- In production via the compiled files in `public/vendor/spatie-media-library-extensions/`
