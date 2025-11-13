import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/funciones.js',
                'resources/js/sweetalert.js',
            ],
            buildDirectory: '../build',
            refresh: true,
        }),
    ],
});
