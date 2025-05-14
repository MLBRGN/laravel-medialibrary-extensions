import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/media-library-extensions.js',
                'resources/css/media-library-extensions.scss',
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
                    if (chunk.name === 'media-library-extensions') {
                        return 'js/media-library-extensions.js'
                    }
                    return 'js/[name].js'
                },
                assetFileNames: (chunk) => {
                    if (chunk.name?.endsWith('media-library-extensions')) {
                        console.log('test');
                        return 'css/media-library-extensions.css';
                    }
                    return 'css/[name][extname]'
                },
            },
        },
    },
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
        }
    },
})
