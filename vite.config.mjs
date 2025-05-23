import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            hotFile: 'public/vendor/media-library-extensions/media-library-extensions.hot', // Most important lines
            buildDirectory: 'vendor/media-library-extensions', // Most important lines
            input: [
                'resources/css/app.scss',
                'resources/js/app.js',
                'resources/css/modal-plain.scss',
                'resources/js/modal-plain.js'
            ],
            refresh: true,
            // input: [
            //     'resources/js/media-library-extensions.js',
            //     'resources/css/media-library-extensions.scss',
            // ],
            // refresh: true,
        }),
    ],
    // build: {
    //     manifest: false,
    //     outDir: 'dist',
    //     rollupOptions: {
    //         output: {
    //             entryFileNames: (chunk) => {
    //                 if (chunk.name === 'media-library-extensions') {
    //                     return 'js/media-library-extensions.js'
    //                 }
    //                 return 'js/[name].js'
    //             },
    //             assetFileNames: (chunk) => {
    //                 if (chunk.name?.endsWith('media-library-extensions')) {
    //                     console.log('test');
    //                     return 'css/media-library-extensions.css';
    //                 }
    //                 return 'css/[name][extname]'
    //             },
    //         },
    //     },
    // },
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
        }
    },
})
