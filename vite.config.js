import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@core-scss': `${path.resolve(__dirname, './resources/styles/@core')}/`,
            '@/': `${path.resolve(__dirname, './resources/js')}/`,
            '@themeConfig': `${path.resolve(__dirname, './themeConfig.js')}/`,
            '@core': `${path.resolve(__dirname, './resources/js/@core')}/`,
            '@layouts': `${path.resolve(__dirname, './resources/js/@layouts')}/`,
            '@images': `${path.resolve(__dirname, './resources/images')}/`,
            '@styles': `${path.resolve(__dirname, './resources/styles')}/`,
            '@configured-variables': `${path.resolve(__dirname, './resources/styles/variables/_template.scss')}/`,
            '@db': `${path.resolve(__dirname, './resources/js/plugins/fake-api/handlers')}/`,
            '@api-utils': `${path.resolve(__dirname, './resources/js/plugins/fake-api/utils')}/`,
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
        }
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
    build: {
        sourcemap: false,
    }
});
