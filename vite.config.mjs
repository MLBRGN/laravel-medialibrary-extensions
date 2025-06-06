import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app-bootstrap-5.js',
                'resources/js/app-plain.js',
                'resources/js/shared/lite-youtube.js',
            ],
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
                entryFileNames: '[name].js',
                assetFileNames: '[name].css',
                // You can customize manualChunks here if needed
            },
        },
        manifest: true,  // better to enable manifest for multi-entry builds
    },
})
