import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path'
import fs from 'fs'

// Paths
const resourcesDir = path.resolve(__dirname, 'resources')
const jsDir = path.resolve(resourcesDir, 'js')
const cssDir = path.resolve(resourcesDir, 'css')

/**
 * Recursively collect JS/CSS entry files.
 *
 * Example:
 * resources/js/plain/modal.js
 * =>
 * js/plain/modal
 */
function getEntriesRecursive(dir, exts = ['.js']) {
    return fs.readdirSync(dir, { withFileTypes: true }).reduce((entries, entry) => {
        const fullPath = path.join(dir, entry.name)

        // Recurse into subdirectories
        if (entry.isDirectory()) {
            return {
                ...entries,
                ...getEntriesRecursive(fullPath, exts),
            }
        }

        // Ignore unsupported files and partials
        if (
            !exts.some(ext => entry.name.endsWith(ext)) ||
            entry.name.startsWith('_')
        ) {
            return entries
        }

        // Build entry name relative to /resources
        let name = path.relative(resourcesDir, fullPath)

        // Remove extension
        name = name.replace(
            new RegExp(`(${exts.map(ext => ext.replace('.', '\\.')).join('|')})$`),
            ''
        )

        // Normalize slashes for Windows
        name = name.split(path.sep).join('/')

        entries[name] = path.relative(__dirname, fullPath)

        return entries
    }, {})
}

// Build entries object
const entries = {
    ...getEntriesRecursive(jsDir, ['.js']),
    ...getEntriesRecursive(cssDir, ['.scss', '.css']),
}

export default defineConfig({
    plugins: [
        laravel({
            input: entries,
            publicDirectory: 'public',
            refresh: true,
        }),
    ],

    build: {
        outDir: 'dist',
        emptyOutDir: true,
        manifest: false,

        rollupOptions: {
            external: (id, importer) => {
                if (!importer) return false

                // Keep bundled
                if (importer.endsWith('/image-editor.js')) {
                    return false
                }

                // Externalize package
                return id === '@mlbrgn/medialibrary-extensions'
            },

            output: {
                // Preserve folder structure from entry names
                entryFileNames: '[name].js',

                // Put chunks in their own folder
                chunkFileNames: 'chunks/[name].js',

                // Preserve asset paths
                assetFileNames: '[name][extname]',
            },
        },
    },

    css: {
        preprocessorOptions: {
            scss: {
                quietDeps: true,

                silenceDeprecations: [
                    'color-functions',
                    'global-builtin',
                    'import',
                ],
            },
        },
    },

    resolve: {
        alias: {
            '@': resourcesDir,
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
        },
    },

    /**
     * Static assets copied directly to dist/
     *
     * Put favicon.ico inside:
     * resources/public/favicon.ico
     *
     * or use:
     * public/favicon.ico
     */
    publicDir: 'public',
})
