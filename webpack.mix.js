const mix = require('laravel-mix')

mix.webpackConfig({
    output: {
        chunkFilename: 'js/[name].[contenthash].js',
    }
})

mix.extend('translations', new class {
  webpackRules() {
    return {
      test: /\/lang\/index\.js$/,
      loader: '@kirschbaum-development/laravel-translations-loader/all',
    }
  }
});

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.vue()
   .react()
   .js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .copyDirectory('resources/images', 'public/images')
   .copyDirectory('resources/twemoji/assets/72x72', 'public/twemoji/assets/72x72')
   .options({ legacyNodePolyfills: false })
   .translations()
   .version();
