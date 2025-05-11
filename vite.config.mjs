import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/mediaPreviewModal.js',
                // 'resources/css/_media-preview.scss',
            ],
            refresh: true,
        }),
    ],
    build: {
        manifest: true,
        outDir: 'dist',
        rollupOptions: {
            output: {
                assetFileNames: 'assets/[name].[hash][extname]',
                entryFileNames: 'assets/[name].[hash].js',
            },
        },
    },
})
