const elixir = require('laravel-elixir'),
    assetPath = './resources/assets/',
    ltePath = './resources/assets/lte/',
    nodePath = './node_modules/'
    ;
require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |1
 */

elixir(mix => {
    mix.styles([
        assetPath + 'bootstrap/css/bootstrap.min.css',
        ltePath + 'css/font-awesome.css',
        ltePath + 'css/AdminLTE.css',
        ltePath + 'css/skins/skin-blue.css',
        ltePath + 'plugins/icheck/square/blue.css'
    ], 'public/css/app.css');

    /*mix.webpack('app.js');*/

    mix.scripts([
        ltePath + 'plugins/jquery/jquery.min.js',
        assetPath + 'bootstrap/js/bootstrap.js',
        ltePath + 'plugins/fastclick/fastclick.min.js',
        ltePath + 'plugins/form-serialize/jquery.serializejson.min.js',
        ltePath + 'plugins/icheck/icheck.js'
    ], 'public/js/vendor.js');

    mix.scripts([
        ltePath + 'js/app.js',
        assetPath + 'js/project.js'
    ], 'public/js/admin.js');

    mix.scripts([
        ltePath + 'js/html5shiv.js',
        ltePath + 'js/respond.js'
    ], 'public/js/ie-support.js');

    mix.copy(nodePath + 'font-awesome/fonts/*.{ttf,woff,woff2,eof,svg}', 'public/fonts');
    mix.copy(nodePath + 'bootstrap-sass/assets/fonts/bootstrap/*.{ttf,woff,woff2,eof,svg}', 'public/fonts');
    mix.copy(ltePath + 'fonts/*', 'public/fonts');

    mix.copy(ltePath + 'img', 'public/img/lte'); // lte image
    mix.copy(ltePath + 'plugins/icheck/square/blue.png', 'public/img');

    mix.version([
        'css/app.css',
        'js/vendor.js',
        'js/admin.js',
        'js/ie-support.js',
        'js/app.js'
    ]);
});
