const mix = require('laravel-mix');

mix.webpackConfig({
    output: {
        chunkFilename: 'js/[name].[contenthash].js',
    }
});

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.vue()
   .js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .js('resources/js/print.js', 'public/js')
   .sass('resources/sass/print.scss', 'public/css')
   .copyDirectory('resources/images', 'public/images');
