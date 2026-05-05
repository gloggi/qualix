import { defineConfig } from 'vite';
import { readdirSync, statSync } from 'fs';
import { resolve } from 'path';
import { fileURLToPath } from 'url';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import laravelTranslations from 'vite-plugin-laravel-translations';
import Components from 'unplugin-vue-components/vite';
import { BootstrapVueNextResolver } from 'bootstrap-vue-next';

const __dirname = fileURLToPath(new URL('.', import.meta.url));
const bvnSubdirs = ['components', 'directives'];
const bvnComponents = bvnSubdirs.flatMap(sub => {
  const dir = resolve(__dirname, `node_modules/bootstrap-vue-next/dist/src/${sub}`);
  return readdirSync(dir)
    .filter(name => statSync(resolve(dir, name)).isDirectory())
    .map(name => `bootstrap-vue-next/${sub}/${name}`);
});

export default defineConfig({
  server: {
    host: true,
    origin: 'http://localhost:5173',
    watch: {
      ignored: ['**/.env*'],
    },
    allowedHosts: [
      'localhost',
    ],
    cors: {
      origin: [
        'http://localhost:5173',
        'http://localhost',
      ],
    },
  },
  optimizeDeps: {
    include: bvnComponents,
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
