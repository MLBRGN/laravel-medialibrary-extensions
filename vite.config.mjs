import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path'
import fs from 'fs'

const jsDir = path.resolve(__dirname, 'resources/js')
const sharedDir = path.resolve(jsDir, 'shared')
const plainDir = path.resolve(jsDir, 'plain')
const bootstrap5Dir = path.resolve(jsDir, 'bootstrap-5')

const faviconPath = path.resolve(__dirname, 'resources/assets/favicon.ico');

function getJsEntries(dir, prefix) {
    return fs
        .readdirSync(dir)
        .filter(file =>
            file.endsWith('.js') &&
            !file.startsWith('_') &&
            fs.statSync(path.join(dir, file)).isFile()
        )
        .reduce((entries, file) => {
            const name = file.replace('.js', '')
            entries[`${prefix}/${name}`] = path.relative(__dirname, path.join(dir, file))
            return entries
        }, {})
}

const entries = {
    ...getJsEntries(plainDir, 'plain'),
    ...getJsEntries(bootstrap5Dir, 'bootstrap-5'),
    ...getJsEntries(sharedDir, 'shared'),
    ...getJsEntries(jsDir, 'root'),
    favicon: faviconPath,
}

export default defineConfig({
    plugins: [
        laravel({
            input: entries,
            publicDirectory: 'public',
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources'),
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
        },
    },
    build: {
        outDir: 'dist',
        emptyOutDir: true,
        rollupOptions: {
            // determine if file is demo.js, if os bundle media-library-extensions (image-editor) otherwise not
            external: (id, importer) => {
                // Importer not provided → don't externalize
                if (!importer) return false

                // If importer is demo.js → always bundle everything
                // if (importer.endsWith('/demo.js')) {
                //     return false
                // }

                // If importer is image-editor.js → always bundle everything
                if (importer.endsWith('/image-editor.js')) {
                    return false
                }

                // For all other files → externalize peer dependency
                return id === '@mlbrgn/media-library-extensions'
            },
            output: {
                // JS entry points go in js/
                entryFileNames: 'js/[name].js',
                // JS chunks go in js/
                chunkFileNames: 'js/[name].js',
                // CSS and other assets
                assetFileNames: assetInfo => {
                    if (assetInfo.name && assetInfo.name.endsWith('.css')) {
                        return 'css/[name][extname]';
                    }
                    return 'assets/[name][extname]';
                },
            },
        },
        manifest: false,
    },
})
