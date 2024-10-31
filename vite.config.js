import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'; // Import du plugin Vue

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue(), // Ajout du plugin Vue
    ],
    resolve: {
        alias: {
          'vue': 'vue/dist/vue.esm-bundler.js',
        },
      },
      build: {
        outDir: 'public/build',  // Définit la sortie du build
        manifest: true,  // Génère un fichier manifest.json pour Laravel
        rollupOptions: {
            output: {
                chunkFileNames: 'js/[name].[hash].js',
                entryFileNames: 'js/[name].[hash].js',
                assetFileNames: 'assets/[name].[hash][extname]',
            },
        },
    },
});
