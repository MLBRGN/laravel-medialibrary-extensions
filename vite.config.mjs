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
            buildDirectory: 'public', // optional
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources'),
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
        },
    },
})
