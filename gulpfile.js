process.env.DISABLE_NOTIFIER = true;

var gulp     = require('gulp'),
    elixir   = require('laravel-elixir'),
    lessglob = require('less-plugin-glob');

// require livereload when not production
if (!elixir.config.production)
    require('laravel-elixir-livereload');

// autoprefixer settings
elixir.config.autoprefix = {
    remove: false,
    cascade: false,
    browsers: [ 'last 2 versions' ]
};

// javascript files for the dashboard in resources/assets/js/
var jsDashboard = [
    'dashboard.js'
];

// javascript files for the public site in resources/assets/js/
var jsLocal = [
    'site-vars.js',
    'contact.js',
    'subscription.js',
    'app.js'
];

// javascript files in bower_components/ for libraries
var jsBower = [
    'jquery/dist/jquery.min.js',
    'bootstrap/dist/js/bootstrap.min.js',
];

var jsDashboardBower = [
    'Sortable/Sortable.js',
    'datetimepicker/jquery.datetimepicker.js',
    'simplemde/dist/simplemde.min.js'
];

// less import path locations other than resources/assets/less/
var lessPaths = [ 'bower_components' ];

elixir(function(mix) {
    // compile the project
    mix
        .copy('bower_components/bootstrap/dist/fonts/**', 'public/fonts')
        .copy('bower_components/font-awesome/fonts/**', 'public/fonts')
        .less('dashboard.less', 'public/css/dashboard.css', {
            paths: lessPaths,
            plugins: [ lessglob ]
        })
        .less('app.less', 'public/css/app.css', {
            paths: lessPaths,
            plugins: [ lessglob ]
        })
        .scripts(jsLocal, 'public/js/app.js', 'resources/assets/js/')
        .scripts(jsDashboard, 'public/js/dashboard.js', 'resources/assets/js/')
        .scripts(jsBower, 'public/js/lib.js', 'bower_components/')
        .scripts(jsDashboardBower, 'public/js/lib-dashboard.js', 'bower_components/')
        .version([ 'css/dashboard.css', 'css/app.css', 'js/dashboard.js', 'js/app.js', 'js/lib.js', 'js/lib-dashboard.js' ]);

    // start livereload when not production
    if (!elixir.config.production)
        mix.livereload();
});
