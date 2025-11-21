import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path'
import fs from 'fs'

const jsDir = path.resolve(__dirname, 'resources/js')
const sharedDir = path.resolve(jsDir, 'shared')
const plainDir = path.resolve(jsDir, 'plain')
const bootstrap5Dir = path.resolve(jsDir, 'bootstrap-5')

// function getJsFiles(dir) {
//     return fs
//         .readdirSync(dir)
//         .filter(file => file.endsWith('.js') && fs.statSync(path.join(dir, file)).isFile())
//         .map(file => path.relative(__dirname, path.join(dir, file)))
// }

function getJsFiles(dir) {
    return fs
        .readdirSync(dir)
        // ✅ include only .js files not starting with "_"
        .filter(file =>
            file.endsWith('.js') &&
            !file.startsWith('_') &&
            fs.statSync(path.join(dir, file)).isFile()
        )
        .map(file => path.relative(__dirname, path.join(dir, file)))
}

console.log(getJsFiles(plainDir));
const faviconPath = path.resolve(__dirname, 'resources/assets/images/favicon.ico');

const inputFiles = [
    ...getJsFiles(jsDir),
    ...getJsFiles(sharedDir),
    ...getJsFiles(plainDir),
    ...getJsFiles(bootstrap5Dir),
    faviconPath,
]

console.log(inputFiles)

export default defineConfig({
    plugins: [
        laravel({
            input: inputFiles,
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
            output: {
                entryFileNames: '[name]-[hash].js',      // for entry points with hash for cache busting
                chunkFileNames: '[name]-[hash].js',      // for code-split chunks with hash for cache busting
                assetFileNames: '[name]-[hash][extname]', // assets with hash for cache busting

            },
        },
        manifest: true, // Enable manifest for asset versioning
    },
})
