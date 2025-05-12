import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/mediaPreviewModal.js',
                'resources/css/media-preview.scss',
            ],
            refresh: true,
        }),
    ],
    build: {
        manifest: false,
        outDir: 'dist',
        rollupOptions: {
            output: {
                entryFileNames: (chunk) => {
                    if (chunk.name === 'mediaPreviewModal') {
                        return 'js/mediaPreviewModal.js'
                    }

                    return 'js/[name].js'
                },
                assetFileNames: (chunk) => {
                    if (chunk.name?.endsWith('media-preview')) {
                        console.log('test');
                        return 'css/media-preview.css';
                    }

                    return 'css/[name][extname]'
                },
            },
        },
    },
})
