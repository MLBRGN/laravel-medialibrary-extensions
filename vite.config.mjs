import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/app.scss'
            ],
            publicDirectory: 'public',
            // buildDirectory: '', // empty => directly in public
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
                entryFileNames: 'app.js',
                assetFileNames: 'app.css',
                // entryFileNames: 'vendor/medialibrary-extensions/app.js',// JavaScript entry points,
                // assetFileNames: 'vendor/medialibrary-extensions/app.css',// CSS, images, fonts, etc.

            },
        },
        manifest: false, // no manifest needed
    }
})
