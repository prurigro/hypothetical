// include packages
const gulp = require("gulp"),
    gUtil = require("gulp-util"),
    gSass = require("gulp-sass"),
    gSassGlob = require("gulp-sass-glob"),
    gConcat = require("gulp-concat"),
    gPlumber = require("gulp-plumber"),
    gUglify = require("gulp-uglify"),
    gModernizr = require("gulp-modernizr"),
    gBabel = require("gulp-babel"),
    gPostCSS = require("gulp-postcss"),
    autoprefixer = require("autoprefixer");

// determine if gulp has been run with --production
const prod = gUtil.env.production;

// declare plugin settings and modernizr tests
const sassOutputStyle = prod ? "compressed" : "nested",
    sassIncludePaths = [ "bower_components" ],
    autoprefixerSettings = { remove: false, cascade: false, browsers: [ "last 6 versions" ] },
    modernizrTests = [];

// javascript files for the public site
const jsPublic = [
    "resources/assets/js/site-vars.js",
    "resources/assets/js/contact.js",
    "resources/assets/js/subscription.js",
    "resources/assets/js/app.js"
];

// javascript libraries for the public site
const jsPublicLibs = [
    "bower_components/jquery/dist/jquery.js",
    "bower_components/bootstrap-sass/assets/javascripts/bootstrap.js",
    "bower_components/jQuery.stickyFooter/assets/js/jquery.stickyfooter.js"
];

// javascript files for the dashboard
const jsDashboard = [
    "resources/assets/js/dashboard.js"
];

// javascript libraries for the dashboard
const jsDashboardLibs = [
    "bower_components/jquery/dist/jquery.js",
    "bower_components/bootstrap-sass/assets/javascripts/bootstrap.js",
    "bower_components/Sortable/Sortable.js",
    "bower_components/datetimepicker/build/jquery.datetimepicker.full.js",
    "bower_components/simplemde/dist/simplemde.min.js"
];

// paths to folders containing fonts that should be copied to public/fonts/
const fontPaths = [
    "resources/assets/fonts/**",
    "bower_components/bootstrap-sass/assets/fonts/**/*",
    "bower_components/fontawesome/fonts/**"
];

// function to handle gulp-plumber errors
function plumberError(err) {
    console.log(err);
    this.emit("end");
}

// function to handle the processing of sass files
function processSass(filename) {
    return gulp.src("resources/assets/sass/" + filename + ".scss")
        .pipe(gPlumber(plumberError))
        .pipe(gSassGlob())
        .pipe(gSass({ outputStyle: sassOutputStyle, includePaths: sassIncludePaths }))
        .pipe(gPostCSS([ autoprefixer(autoprefixerSettings) ]))
        .pipe(gConcat(filename + ".css"))
        .pipe(gulp.dest("public/css/"));
}

// function to handle the processing of javascript files
function processJavaScript(ouputFilename, inputFiles, es6) {
    const javascript = gulp.src(inputFiles)
        .pipe(gPlumber(plumberError))
        .pipe(gConcat(ouputFilename + ".js"));

    if (es6) { javascript.pipe(gBabel({ presets: [ "es2015" ] })); }
    if (prod) { javascript.pipe(gUglify()); }
    return javascript.pipe(gulp.dest("public/js/"));
}

// gulp task for public styles
gulp.task("sass-public", function() {
    return processSass("app");
});

// gulp task for dashboard styles
gulp.task("sass-dashboard", function() {
    return processSass("dashboard");
});

// gulp task for public javascript
gulp.task("js-public", function() {
    return processJavaScript("app", jsPublic, true);
});

// gulp task for public javascript libraries
gulp.task("js-public-libs", function() {
    return processJavaScript("lib", jsPublicLibs, false);
});

// gulp task for dashboard javascript
gulp.task("js-dashboard", function() {
    return processJavaScript("dashboard", jsDashboard, true);
});

// gulp task for dashboard javascript libraries
gulp.task("js-dashboard-libs", function() {
    return processJavaScript("lib-dashboard", jsDashboardLibs, false);
});

// gulp task to copy fonts
gulp.task("fonts", function() {
    return gulp.src(fontPaths)
        .pipe(gPlumber(plumberError))
        .pipe(gulp.dest("public/fonts/"));
});

// gulp task for modernizr
gulp.task("modernizr", function() {
    const modernizr = gulp.src([ "public/js/lib.js", "public/js/app.js", "public/css/app.css" ])
        .pipe(gModernizr({
            tests: modernizrTests,
            excludeTests: [ "hidden" ],
            crawl: false,
            options: [ "setClasses", "addTest", "html5printshiv", "testProp", "fnBind" ]
        }))
        .pipe(gPlumber(plumberError))
        .pipe(gConcat("modernizr.js"));

    // minify if running gulp with --production
    if (prod) { modernizr.pipe(gUglify()); }
    return modernizr.pipe(gulp.dest("public/js/"));
});

// gulp watch task
gulp.task("watch", function() {
    const gLiveReload = require("gulp-livereload");

    const liveReloadUpdate = function(wait) {
        setTimeout(function() {
            gLiveReload.changed(".");
        }, wait || 1);
    };

    gLiveReload.listen();
    gulp.watch(jsPublic, [ "js-public" ]).on("change", liveReloadUpdate);
    gulp.watch(jsDashboard, [ "js-dashboard" ]).on("change", liveReloadUpdate);
    gulp.watch([ "app/**/*.php", "resources/views/**/*.blade.php" ]).on("change", liveReloadUpdate);

    gulp.watch("resources/assets/sass/**/*.scss", [ "sass-public", "sass-dashboard" ]).on("change", function() {
        liveReloadUpdate(1000);
    });
});

// gulp default task
gulp.task("default", [
    "sass-public",
    "sass-dashboard",
    "js-public",
    "js-public-libs",
    "js-dashboard",
    "js-dashboard-libs",
    "fonts"
]);
