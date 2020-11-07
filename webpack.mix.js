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

mix.sass('resources/sass/app.scss', 'public/css')
    .sass('resources/sass/admin/app.scss', 'public/admin/css')
    .stylus('resources/stylus/admin/admin.styl', 'public/admin/css', {
        use: [
            require('rupture')()
        ]
    })
    .stylus('resources/stylus/styles.styl', 'public/css', {
        use: [
            require('rupture')()
        ]
    })
    .js('resources/js/admin/app.js', 'public/admin/js')
    .js('resources/js/app.js', 'public/js');

if (mix.inProduction()) {
    mix.version();
}