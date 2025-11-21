import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path'
import fs from 'fs'

const jsDir = path.resolve(__dirname, 'resources/js')
const sharedDir = path.resolve(jsDir, 'shared')
const plainDir = path.resolve(jsDir, 'plain')
const bootstrap5Dir = path.resolve(jsDir, 'bootstrap-5')

function getJsFiles(dir) {
    return fs
        .readdirSync(dir)
        // include only .js files not starting with "_"
        .filter(file =>
            file.endsWith('.js') &&
            !file.startsWith('_') &&
            fs.statSync(path.join(dir, file)).isFile()
        )
        .map(file => path.relative(__dirname, path.join(dir, file)))
}

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
                // JS entry points go in js/
                entryFileNames: 'js/[name].js',
                // JS chunks go in js/
                chunkFileNames: 'js/[name].js',
                // CSS and other assets
                assetFileNames: assetInfo => {
                    if (assetInfo.name && assetInfo.name.endsWith('.css')) {
                        return 'css/[name][extname]';
                    }
                    return 'assets/[name][extname]'; // images, fonts, etc
                },
            },
        },
        manifest: false,
    },
})
