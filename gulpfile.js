// Core packages
const gulp = require("gulp"),
    minimist = require("minimist"),
    log = require("fancy-log"),
    insert = require("gulp-insert"),
    plumber = require("gulp-plumber"),
    concat = require("gulp-concat");

// Sass and CSS packages
const sass = require("gulp-sass"),
    sassGlob = require("gulp-sass-glob"),
    postCSS = require("gulp-postcss"),
    autoprefixer = require("autoprefixer"),
    cleanCSS = require("gulp-clean-css");

// Javascript packages
const babel = require("gulp-babel"),
    stripDebug = require("gulp-strip-debug"),
    uglify = require("gulp-uglify");

// Vue packages
const browserify = require("browserify"),
    vueify = require("vueify-next"),
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
const sassOutputStyle = isProduction ? "compressed" : "expanded",
    sassPaths = [ "node_modules" ],
    autoprefixerSettings = { remove: false, cascade: false },
    vuePaths = [ "./node_modules", "./resources/components", "./resources/js" ];

// Javascript files for the public site
const jsPublic = "resources/js/app.js";

// Javascript libraries for the public site
const jsPublicLibs = [
    "node_modules/jquery/dist/jquery.js",
    "node_modules/gsap/dist/gsap.js",
    "node_modules/what-input/dist/what-input.js"
];

// Javascript files for the dashboard
const jsDashboard = [
    "resources/js/dashboard.js"
];

// Javascript libraries for the dashboard
const jsDashboardLibs = [
    "node_modules/jquery/dist/jquery.js",
    "node_modules/popper.js/dist/umd/popper.js",
    "node_modules/bootstrap/dist/js/bootstrap.js",
    "node_modules/flatpickr/dist/flatpickr.js",
    "node_modules/sortablejs/Sortable.js",
    "node_modules/list.js/dist/list.js",
    "node_modules/easymde/dist/easymde.min.js"
];

// CSS libraries for the dashboard
const cssDashboardLibs = [
    "node_modules/flatpickr/dist/flatpickr.css",
    "node_modules/easymde/dist/easymde.min.css",
    "node_modules/spinkit/spinkit.css"
];

// Paths to folders containing fonts that should be copied to public/fonts/
const fontPaths = [
    "resources/fonts/**",
    "node_modules/@fortawesome/fontawesome-free/webfonts/**"
];

// Handle errors
function handleError(err) {
    log.error(err);
    this.emit("end");
}

// Process sass
function processSass(filename) {
    const css = gulp.src(`resources/sass/${filename}.scss`)
        .pipe(plumber(handleError))
        .pipe(sassGlob())
        .pipe(sass({ outputStyle: sassOutputStyle, includePaths: sassPaths }))
        .pipe(postCSS([ autoprefixer(autoprefixerSettings) ]))
        .pipe(concat(`${filename}.css`))
        .pipe(gulp.dest("public/css/"));

    if (!isProduction) {
        css.pipe(browserSync.stream({ match: `**/${filename}.css` }));
    }

    return css;
}

// Process css
function processCSS(outputFilename, inputFiles) {
    const css = gulp.src(inputFiles)
        .pipe(plumber(handleError))
        .pipe(postCSS([ autoprefixer(autoprefixerSettings) ]))
        .pipe(concat(`${outputFilename}.css`));

    if (isProduction) {
        css.pipe(cleanCSS());
    }

    return css.pipe(gulp.dest("public/css/"));
}

// Process vue
function processVue(outputFilename, inputFile) {
    const processedDir = "storage/app/",
        processedFile = `__${outputFilename}.js`;

    const preProcess = () => {
        const javascript = gulp.src([ inputFile ]);

        if (isProduction) {
            javascript.pipe(insert.transform(function(contents) {
                return contents.replace(/vue\.js/, "vue.min.js");
            }));
        }

        return javascript.pipe(concat(processedFile))
            .pipe(gulp.dest(processedDir));
    };

    const process = () => {
        const javascript = browserify({
            entries: [ processedDir + processedFile ],
            paths: vuePaths
        }).transform("babelify")
            .transform(vueify)
            .bundle()
            .on("error", handleError)
            .pipe(source(`${outputFilename}.js`))
            .pipe(buffer());

        if (isProduction) {
            javascript.pipe(stripDebug()).pipe(uglify().on("error", handleError));
        }

        return javascript.pipe(gulp.dest("public/js/"));
    };

    preProcess();
    return process();
}

// Process javascript
function processJavaScript(outputFilename, inputFiles, es6) {
    const javascript = gulp.src(inputFiles)
        .pipe(plumber(handleError))
        .pipe(concat(`${outputFilename}.js`));

    if (es6) {
        if (isProduction) {
            javascript.pipe(babel()).pipe(stripDebug()).pipe(uglify());
        } else {
            javascript.pipe(babel());
        }
    } else if (isProduction) {
        javascript.pipe(stripDebug()).pipe(uglify());
    }

    return javascript.pipe(gulp.dest("public/js/"));
}

// Task for error page styles
gulp.task("sass-error", () => {
    return processSass("error");
});

// Task for public styles
gulp.task("sass-public", () => {
    return processSass("app");
});

// Task for dashboard styles
gulp.task("sass-dashboard", () => {
    return processSass("dashboard");
});

// Task for dashboard css libraries
gulp.task("css-dashboard-libs", () => {
    return processCSS("lib-dashboard", cssDashboardLibs);
});

// Task for public javascript
gulp.task("js-public", () => {
    return processVue("app", jsPublic);
});

// Task for public javascript libraries
gulp.task("js-public-libs", () => {
    return processJavaScript("lib", jsPublicLibs, false);
});

// Task for dashboard javascript
gulp.task("js-dashboard", () => {
    return processJavaScript("dashboard", jsDashboard, true);
});

// Task for dashboard javascript libraries
gulp.task("js-dashboard-libs", () => {
    return processJavaScript("lib-dashboard", jsDashboardLibs, false);
});

// Task to copy fonts
gulp.task("fonts", (done) => {
    gulp.src(fontPaths)
        .pipe(plumber(handleError))
        .pipe(gulp.dest("public/fonts/"));

    done();
});

// Task to watch files and run respective tasks when changes occur
gulp.task("watch", () => {
    const browserSyncReload = (done) => {
        browserSync.reload();
        done();
    };

    browserSync.init({
        logLevel: "silent",
        baseDir: "./public",
        notify: false,

        ghostMode: {
            clicks: false,
            forms: true,
            scroll: false
        }
    });

    gulp.watch([ "app/**/*.php", "routes/**/*.php", "resources/views/**/*.blade.php" ], gulp.series(browserSyncReload));
    gulp.watch([ "resources/js/**/app.js", "resources/js/mixins/**/*.js", "resources/components/**/*.vue" ], gulp.series("js-public", browserSyncReload));
    gulp.watch("resources/js/**/dashboard.js", gulp.series("js-dashboard", browserSyncReload));
    gulp.watch("resources/sass/**/*.scss", gulp.parallel("sass-public", "sass-dashboard", "sass-error"));
});

// Task to run non-development tasks
gulp.task("default", gulp.parallel(
    "sass-error",
    "sass-public",
    "sass-dashboard",
    "css-dashboard-libs",
    "js-public",
    "js-public-libs",
    "js-dashboard",
    "js-dashboard-libs",
    "fonts"
));
