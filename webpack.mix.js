const mix = require('laravel-mix');

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

mix.js('resources/js/app.js', 'public/js')
   .extract(['vue', 'jquery', 'lodash', 'popper.js', 'axios', 'sweetalert2', 'dropzone', 'instascan-last'])
   .sass('resources/sass/app.scss', 'public/css');

mix.js('resources/js/frontEnd.js', 'public/js');
