const mix = require('laravel-mix');

mix.stylus(__dirname + '/Resources/stylus/theme/default/articles.styl', '../../front/static/css/theme/default', {
    use: [
        require('rupture')()
    ]
})

if (mix.inProduction()) {
    mix.version();
}