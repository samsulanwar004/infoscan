const elixir = require('laravel-elixir'),
    assetPath = './resources/assets/',
    ltePath = './resources/assets/lte/',
    nodePath = './node_modules/';
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
        ltePath + 'css/skins/skin-red-light.css',
        ltePath + 'plugins/icheck/square/blue.css',
        ltePath + 'plugins/datepicker/datepicker3.css',
        ltePath + 'plugins/daterangepicker/daterangepicker.css',
        ltePath + 'plugins/easyui/easyui.css',
        ltePath + 'css/taggd.css',
        ltePath + 'plugins/multiselect/bootstrap-multiselect.css'
    ], 'public/css/app.css');

    mix.styles([
        ltePath + 'css/taggd.css'
    ], 'public/css/taggd.css');

    mix.styles([
        ltePath + 'plugins/datetimepicker/bootstrap-datetimepicker.min.css'
    ], 'public/css/datetimepicker.css');

    mix.styles([
        ltePath + 'css/zoom.css'
    ], 'public/css/zoom.css');

    /*mix.webpack('app.js');*/

    mix.scripts([
        ltePath + 'plugins/jquery/jquery.min.js',
        ltePath + 'plugins/daterangepicker/moment.js',
        ltePath + 'plugins/datepicker/bootstrap-datepicker.js',
        ltePath + 'plugins/daterangepicker/daterangepicker.js',
        assetPath + 'bootstrap/js/bootstrap.js',
        ltePath + 'plugins/fastclick/fastclick.min.js',
        ltePath + 'plugins/form-serialize/jquery.serializejson.min.js',
        ltePath + 'plugins/easyui/jquery.easyui.min.js',
        ltePath + 'plugins/easyui/pivot/jquery.pivotgrid.js',
        ltePath + 'plugins/icheck/icheck.js',
        ltePath + 'plugins/tagged/taggd.js',
        ltePath + 'plugins/multiselect/bootstrap-multiselect.js'
    ], 'public/js/vendor.js');

    mix.scripts([
        ltePath + 'js/app.js',
        assetPath + 'js/project.js'
    ], 'public/js/admin.js');

    mix.scripts([
        ltePath + 'plugins/tagged/taggd.js'
    ], 'public/js/taggd.js');

    mix.scripts([
        ltePath + 'plugins/elevate/jquery.elevatezoom.js'
    ], 'public/js/elevate.js');

    mix.scripts([
        ltePath + 'plugins/zoom/jquery-ui.js',
        ltePath + 'plugins/zoom/jfmagnify.min.js'
    ], 'public/js/zoom.js');

    mix.scripts([
        ltePath + 'plugins/datetimepicker/bootstrap-datetimepicker.min.js',
        ltePath + 'plugins/datetimepicker/id.js',
    ], 'public/js/datetimepicker.js');

    mix.scripts([
       ltePath + 'plugins/slider/jquery.flexslider.js'
    ], 'public/js/flexslider.js');

    mix.scripts([
        ltePath + 'plugins/chartjs/Chart.bundle.min.js',
    ], 'public/js/Chart.bundle.min.js');

    mix.scripts([
        ltePath + 'js/html5shiv.js',
        ltePath + 'js/respond.js'
    ], 'public/js/ie-support.js');

    mix.styles([
        ltePath + 'plugins/ionrange/css/ion.rangeSlider.css',
        ltePath + 'plugins/ionrange/css/ion.rangeSlider.skinModern.css',
        ltePath + 'plugins/select2/css/select2.css',
    ], 'public/css/report-vendor.css');
    mix.scripts([
        ltePath + 'plugins/cookie/js.cookie.js',
        ltePath + 'plugins/ionrange/js/ion-rangeSlider/ion.rangeSlider.js',
        ltePath + 'plugins/select2/js/select2.js',
    ], 'public/js/report-vendor.js');

    mix.copy(ltePath + 'fonts/*.{ttf,woff,woff2,eof,svg}', 'public/fonts');
    mix.copy(nodePath + 'bootstrap-sass/assets/fonts/bootstrap/*.{ttf,woff,woff2,eof,svg}', 'public/fonts');
    mix.copy(ltePath + 'fonts/*', 'public/fonts');

    mix.copy(ltePath + 'img', 'public/img/lte'); // lte image
    mix.copy(ltePath + 'plugins/easyui/images', 'public/img/easyui'); // lte image
    mix.copy(ltePath + 'plugins/icheck/square/blue.png', 'public/img');
    mix.copy(ltePath + 'plugins/ionrange/img/sprite-skin-modern.png', 'public/img');

    mix.version([
        'css/app.css',
        'css/taggd.css',
        'css/zoom.css',
        'js/vendor.js',
        'js/admin.js',
        'js/ie-support.js',
        'js/app.js',
        'js/elevate.js',
        'js/taggd.js',
        'js/report-vendor.js',
        'css/report-vendor.css',
        'css/datetimepicker.css',
        'js/datetimepicker.js',
        'js/Chart.bundle.min.js',
        'js/zoom.js',
        'js/flexslider.js',
    ]);
});
