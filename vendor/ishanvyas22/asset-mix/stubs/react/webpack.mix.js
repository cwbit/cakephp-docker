const mix = require('laravel-mix');

mix.setPublicPath('./webroot')
    .js('assets/js/app.js', 'webroot/js').react()
    .sass('assets/sass/app.scss', 'webroot/css')
    .version();
