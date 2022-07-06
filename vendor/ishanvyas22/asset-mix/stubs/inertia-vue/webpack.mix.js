const mix = require('laravel-mix');
const path = require('path');

mix.setPublicPath('./webroot')
    .js('assets/js/app.js', 'webroot/js').vue()
    .sass('assets/sass/app.scss', 'webroot/css')
    .webpackConfig({
        output: {
            chunkFilename: 'js/[name].js?id=[chunkhash]'
        },
        resolve: {
            alias: {
                vue$: 'vue/dist/vue.runtime.esm.js',
                '@': path.resolve('assets/js'),
            },
        },
    })
    .version()
    .sourceMaps();
