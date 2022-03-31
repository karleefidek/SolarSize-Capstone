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
    .vue()
<<<<<<< HEAD
    .sourceMaps(false, 'source-map')
=======
	.sourceMaps(false,'source-map')
>>>>>>> 82e934613b2e0abdc7b7f6a131aa6c17490eac1b
    .sass('resources/sass/app.scss', 'public/css');
