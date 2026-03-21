import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path'
import fs from 'fs'

// Paths
const jsDir = path.resolve(__dirname, 'resources/js')
const cssDir = path.resolve(__dirname, 'resources/css')
const faviconPath = path.resolve(__dirname, 'resources/assets/favicon.ico')
const resourcesDir = path.resolve(__dirname, 'resources')

function getEntriesRecursive(dir, exts = ['.js']) {
    return fs.readdirSync(dir, { withFileTypes: true }).reduce((entries, entry) => {
        const fullPath = path.join(dir, entry.name)

        if (entry.isDirectory()) {
            return { ...entries, ...getEntriesRecursive(fullPath, exts) }
        }

        if (!exts.some(ext => entry.name.endsWith(ext)) || entry.name.startsWith('_')) {
            return entries
        }

        // 🔥 KEY CHANGE: relative to /resources, not js/ or css/
        let name = path.relative(resourcesDir, fullPath)

        name = name.replace(new RegExp(`${exts.join('|').replace(/\./g,'\\.')}$`), '')
        name = name.split(path.sep).join('/')

        entries[name] = path.relative(__dirname, fullPath)

        return entries
    }, {})
}

// Build entries object
const entries = {
    ...getEntriesRecursive(jsDir, ['.js']),
    ...getEntriesRecursive(cssDir, ['.scss', '.css']),
    favicon: faviconPath,
}

// console.log('entries', entries)

export default defineConfig({
    plugins: [
        laravel({
            input: entries,
            publicDirectory: 'public',
            refresh: true, // enables Blade auto-refresh during dev
        }),
    ],
    build: {
        outDir: 'dist',
        emptyOutDir: true,
        manifest: false,
        rollupOptions: {
            // Keep your external logic if needed
            external: (id, importer) => {
                if (!importer) return false
                if (importer.endsWith('/image-editor.js')) return false
                return id === '@mlbrgn/media-library-extensions'
            },
            output: {
                entryFileNames: '[name].js',
                chunkFileNames: '[name].js',
                assetFileNames: assetInfo => {
                    if (assetInfo.name?.endsWith('.css')) return '[name][extname]'
                    return 'assets/[name][extname]'
                },
            },
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                quietDeps: true,
                silenceDeprecations: [
                    "color-functions",
                    "global-builtin",
                    "import",
                ],
            },
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources'),
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
        },
    },
})
