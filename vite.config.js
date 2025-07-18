import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import laravelTranslations from 'vite-plugin-laravel-translations';
import Components from 'unplugin-vue-components/vite';
import { BootstrapVueNextResolver } from 'bootstrap-vue-next';

export default defineConfig({
  server: {
    host: true,
    origin: 'http://localhost:5173',
    allowedHosts: [
      'http://localhost',
      'http://localhost:5173',
    ],
    cors: {
      origin: [
        'http://localhost:5173',
        'http://localhost',
      ],
    },
  },
  build: {
    assetsInlineLimit: 0, // necessary so that Vite does not inline any images in prod, which would violate the CSP
  },
  plugins: [
    laravel({
      input: [
        'resources/sass/app.scss',
        'resources/js/app.js'
      ],
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      }
    }),
    laravelTranslations({
      namespace: false,
      absoluteLanguageDirectory: 'lang',
      includeJson: true,
      assertJsonImport: true,
    }),
    Components({
      resolvers: [
        BootstrapVueNextResolver(),
      ],
    })
  ],
  resolve: {
    alias: {
      'vue': 'vue/dist/vue.esm-bundler',
    },
  },
});
