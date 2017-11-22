// include packages
const gulp = require("gulp"),
    gutil = require("gulp-util"),
    plumber = require("gulp-plumber"),
    concat = require("gulp-concat"),
    sass = require("gulp-sass"),
    sassGlob = require("gulp-sass-glob"),
    postCSS = require("gulp-postcss"),
    autoprefixer = require("autoprefixer"),
    babel = require("gulp-babel"),
    stripDebug = require("gulp-strip-debug"),
    uglify = require("gulp-uglify");

// determine if gulp has been run with --production
const prod = gutil.env.production;

// declare plugin settings
const sassOutputStyle = prod ? "compressed" : "nested",
    sassIncludePaths = [ "bower_components" ],
    autoprefixerSettings = { remove: false, cascade: false, browsers: [ "last 6 versions" ] };

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
    "node_modules/gsap/src/uncompressed/TweenMax.js",
    "node_modules/what-input/dist/what-input.js"
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
    "bower_components/list.js/dist/list.js",
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
        .pipe(plumber(plumberError))
        .pipe(sassGlob())
        .pipe(sass({ outputStyle: sassOutputStyle, includePaths: sassIncludePaths }))
        .pipe(postCSS([ autoprefixer(autoprefixerSettings) ]))
        .pipe(concat(filename + ".css"))
        .pipe(gulp.dest("public/css/"));
}

// function to handle the processing of javascript files
function processJavaScript(ouputFilename, inputFiles, es6) {
    const javascript = gulp.src(inputFiles)
        .pipe(plumber(plumberError))
        .pipe(concat(ouputFilename + ".js"));

    if (es6) { javascript.pipe(babel()); }
    if (prod) { javascript.pipe(stripDebug()).pipe(uglify()); }
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
        .pipe(plumber(plumberError))
        .pipe(gulp.dest("public/fonts/"));
});

// gulp watch task
gulp.task("watch", function() {
    const gLiveReload = require("gulp-livereload");

    const liveReloadUpdate = function(files, wait) {
        setTimeout(function() {
            gLiveReload.changed(files);
        }, wait || 1);
    };

    gLiveReload.listen();
    gulp.watch(jsPublic, [ "js-public" ]).on("change", liveReloadUpdate);
    gulp.watch(jsDashboard, [ "js-dashboard" ]).on("change", liveReloadUpdate);
    gulp.watch([ "app/**/*.php", "routes/**/*.php", "resources/views/**/*.blade.php" ]).on("change", liveReloadUpdate);

    gulp.watch("resources/assets/sass/**/*.scss", [ "sass-public", "sass-dashboard" ]).on("change", function(files) {
        liveReloadUpdate(files, 1000);
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
