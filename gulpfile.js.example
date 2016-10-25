const elixir = require('laravel-elixir'),
      acePath = './resources/assets/admin/'
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
    mix.sass('app.scss')
       .webpack('app.js');

    mix.styles([
        acePath + 'css/bootstrap.min.css',
        acePath + 'css/ace.min.css',
        acePath + 'css/ace-ie.min.css',
        acePath + 'css/ace-part2.min.css',
        acePath + 'css/ace-skins.min.css',
        acePath + 'css/font-awesome.min.css',
    ], 'public/css/vendor.css');

    mix.scripts([
        acePath + 'js/jquery-2.1.4.min.js',
        acePath + 'js/html5shiv.min.js',
        acePath + 'js/respond.min.js',
        acePath + 'js/bootstrap.min.js',
        acePath + 'js/ace-elements.min.js',
        acePath + 'js/ace-extra.min.js',
        acePath + 'js/autosize.min.js',
        acePath + 'js/ace.min.js',
    ], 'public/js/vendor.js');

    mix.scripts([
        acePath + 'js/html5shiv.min.js',
        acePath + 'js/respond.min.js',
    ], 'public/js/ie.js');

    mix.version([
        'css/vendor.css',
        'js/vendor.js',
        'js/app.css',
        'js/app.js',
    ]);

    mix.copy(acePath + 'fonts/*.{ttf,woff,woff2,eof,svg}', 'public/fonts');
    mix.copy(acePath + 'css/fonts.googleapis.com.css', 'public/css/fonts.googleapis.com.css');
    mix.copy(acePath + 'css/jquery.mobile.custom.min.js', 'public/css/jquery.mobile.custom.min.js');
    mix.copy(acePath + 'images', 'public/img');
});
