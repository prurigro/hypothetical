// Core packages
const gulp = require("gulp"),
    minimist = require("minimist"),
    log = require("fancy-log"),
    plumber = require("gulp-plumber"),
    concat = require("gulp-concat");

// Sass packages
const sass = require("gulp-sass"),
    sassGlob = require("gulp-sass-glob"),
    postCSS = require("gulp-postcss"),
    autoprefixer = require("autoprefixer");

// Javascript packages
const babel = require("gulp-babel"),
    stripDebug = require("gulp-strip-debug"),
    uglify = require("gulp-uglify");

// Vue packages
const browserify = require("browserify"),
    vueify = require("vueify"),
    source = require("vinyl-source-stream"),
    buffer = require("vinyl-buffer");

// Determine if gulp has been run with --production
const isProduction = minimist(process.argv.slice(2)).production !== undefined;

// Include browsersync when gulp has not been run with --production
let browserSync = undefined;

if (!isProduction) {
    browserSync = require("browser-sync").create();
}

// Declare plugin settings
const sassOutputStyle = isProduction ? "compressed" : "nested",
    sassPaths = [ "bower_components", "node_modules" ],
    autoprefixerSettings = { remove: false, cascade: false, browsers: [ "last 6 versions" ] },
    vuePaths = [ "./bower_components", "./node_modules", "./resources/components", "./resources/assets/js" ];

// Vue file for the public site
const vuePublic = "resources/assets/js/app-vue.js";

// Javascript files for the public site
const jsPublic = [
    "resources/assets/js/site-vars.js",
    "resources/assets/js/nav.js",
    "resources/assets/js/contact.js",
    "resources/assets/js/subscription.js",
    "resources/assets/js/app.js"
];

// Javascript libraries for the public site
const jsPublicLibs = [
    "node_modules/jquery/dist/jquery.js",
    "node_modules/popper.js/dist/umd/popper.js",
    "node_modules/bootstrap/dist/js/bootstrap.js",
    "node_modules/gsap/src/uncompressed/TweenMax.js",
    "node_modules/what-input/dist/what-input.js"
];

// Javascript files for the dashboard
const jsDashboard = [
    "resources/assets/js/dashboard.js"
];

// Javascript libraries for the dashboard
const jsDashboardLibs = [
    "bower_components/jquery/dist/jquery.js",
    "bower_components/bootstrap-sass/assets/javascripts/bootstrap.js",
    "bower_components/Sortable/Sortable.js",
    "bower_components/pickadate/lib/picker.js",
    "bower_components/pickadate/lib/picker.date.js",
    "bower_components/list.js/dist/list.js",
    "bower_components/simplemde/dist/simplemde.min.js"
];

// Paths to folders containing fonts that should be copied to public/fonts/
const fontPaths = [
    "resources/assets/fonts/**",
    "node_modules/font-awesome/fonts/**",
    "bower_components/bootstrap-sass/assets/fonts/**/*"
];

// Handle errors
function handleError(err) {
    log.error(err);
    this.emit("end");
}

// Process sass
function processSass(filename) {
    const css = gulp.src("resources/assets/sass/" + filename + ".scss")
        .pipe(plumber(handleError))
        .pipe(sassGlob())
        .pipe(sass({ outputStyle: sassOutputStyle, includePaths: sassPaths }))
        .pipe(postCSS([ autoprefixer(autoprefixerSettings) ]))
        .pipe(concat(filename + ".css"));

    if (isProduction) {
        return css.pipe(gulp.dest("public/css/"));
    } else {
        return css.pipe(gulp.dest("public/css/")).pipe(browserSync.stream({ match: "**/" + filename + ".css" }));
    }
}

// Process vue
function processVue(ouputFilename, inputFile) {
    const javascript = browserify({
        entries: [ inputFile ],
        paths: vuePaths
    }).transform("babelify")
        .transform(vueify)
        .bundle()
        .on("error", handleError)
        .pipe(source(ouputFilename + ".js"))
        .pipe(buffer());

    if (isProduction) { javascript.pipe(stripDebug()).pipe(uglify().on("error", handleError)); }
    return javascript.pipe(gulp.dest("public/js/"));
}

// Process javascript
function processJavaScript(ouputFilename, inputFiles, es6) {
    const javascript = gulp.src(inputFiles)
        .pipe(plumber(handleError))
        .pipe(concat(ouputFilename + ".js"));

    if (es6) { javascript.pipe(babel()); }
    if (isProduction) { javascript.pipe(stripDebug()).pipe(uglify()); }
    return javascript.pipe(gulp.dest("public/js/"));
}

// Task for public styles
gulp.task("sass-public", function() {
    return processSass("app");
});

// Task for dashboard styles
gulp.task("sass-dashboard", function() {
    return processSass("dashboard");
});

// Task for public vue
gulp.task("js-public-vue", function() {
    return processVue("app-vue", vuePublic);
});

// Task for public javascript
gulp.task("js-public", function() {
    return processJavaScript("app", jsPublic, true);
});

// Task for public javascript libraries
gulp.task("js-public-libs", function() {
    return processJavaScript("lib", jsPublicLibs, false);
});

// Task for dashboard javascript
gulp.task("js-dashboard", function() {
    return processJavaScript("dashboard", jsDashboard, true);
});

// Task for dashboard javascript libraries
gulp.task("js-dashboard-libs", function() {
    return processJavaScript("lib-dashboard", jsDashboardLibs, false);
});

// Task to copy fonts
gulp.task("fonts", function() {
    return gulp.src(fontPaths)
        .pipe(plumber(handleError))
        .pipe(gulp.dest("public/fonts/"));
});

// Task to run tasks when their respective files are changed
const watchReload = function(done) {
    browserSync.reload();
    done();
};

gulp.task("js-public-watch", [ "js-public" ], watchReload);
gulp.task("js-public-vue-watch", [ "js-public-vue" ], watchReload);
gulp.task("js-dashboard-watch", [ "js-dashboard" ], watchReload);

gulp.task("watch", function() {
    browserSync.init({
        logLevel: "silent",
        baseDir: "./public",

        ghostMode: {
            clicks: true,
            forms: true,
            scroll: false
        }
    });

    gulp.watch(jsPublic, [ "js-public-watch" ]);
    gulp.watch(jsDashboard, [ "js-dashboard-watch" ]);
    gulp.watch([ "app/**/*.php", "routes/**/*.php", "resources/views/**/*.blade.php" ]).on("change", browserSync.reload);
    gulp.watch([ vuePublic, "resources/assets/js/mixins/**/*.js", "resources/components/**/*.vue" ], [ "js-public-vue-watch" ]);
    gulp.watch("resources/assets/sass/**/*.scss", [ "sass-public", "sass-dashboard" ]);
});

// Task to run non-development tasks
gulp.task("default", [
    "sass-public",
    "sass-dashboard",
    "js-public-vue",
    "js-public",
    "js-public-libs",
    "js-dashboard",
    "js-dashboard-libs",
    "fonts"
]);
