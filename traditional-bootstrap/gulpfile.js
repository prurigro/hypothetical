// Core packages
const gulp = require("gulp"),
    minimist = require("minimist"),
    log = require("fancy-log"),
    plumber = require("gulp-plumber"),
    concat = require("gulp-concat"),
    ordered = require("ordered-read-streams"),
    fs = require("fs"),
    crypto = require("crypto");

// Sass and CSS packages
const { sass } = require("gulp5-sass-plugin"),
    sassGlob = require("gulp-sass-glob"),
    postCSS = require("gulp-postcss"),
    autoprefixer = require("autoprefixer"),
    cleanCSS = require("gulp-clean-css");

// Javascript packages
const babel = require("gulp-babel"),
    stripDebug = require("gulp-strip-debug"),
    uglify = require("gulp-uglify-es").default;

// Determine if gulp has been run with --production
const isProduction = minimist(process.argv.slice(2)).production !== undefined;

// Declare plugin settings
const sassPaths = "node_modules",
    autoprefixerSettings = { remove: false, cascade: false };

// Include browsersync when gulp has not been run with --production
let browserSync = undefined;

if (!isProduction) {
    browserSync = require("browser-sync").create();
}

// Environment
process.env.NODE_ENV = isProduction ? "production" : "development";
process.env.SASS_PATH = sassPaths;

// Javascript files for the public site
const jsPublic = [
    "resources/js/site-vars.js",
    "resources/js/nav.js",
    "resources/js/contact.js",
    "resources/js/subscription.js",
    "resources/js/app.js"
];

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
    "node_modules/bootstrap/dist/js/bootstrap.bundle.js",
    "node_modules/flatpickr/dist/flatpickr.js",
    "node_modules/sortablejs/Sortable.js",
    "node_modules/list.js/dist/list.js",
    "node_modules/easymde/dist/easymde.min.js",
    "node_modules/autonumeric/dist/autoNumeric.js"
];

// CSS libraries for the dashboard
const cssDashboardLibs = [
    "node_modules/flatpickr/dist/flatpickr.css",
    "node_modules/easymde/dist/easymde.min.css",
    "node_modules/spinkit/spinkit.css"
];

// Paths to folders containing fonts that should be copied to public/fonts/
const fontPaths = [
    "resources/fonts/*",
    "node_modules/@fortawesome/fontawesome-free/webfonts/*"
];

// Handle errors
function handleError(err) {
    log.error(err);
    this.emit("end");
}

// Takes an array of files and returns an stream of gulp sources
function orderedGulpSources(array) {
    return ordered(array.map(function(item) {
        return gulp.src(item);
    }));
}

// Process sass
function processSass(filename) {
    const css = gulp.src(`resources/sass/${filename}.scss`)
        .pipe(plumber(handleError))
        .pipe(sassGlob())
        .pipe(sass({ quietDeps: true, silenceDeprecations: [ "import" ] }))
        .pipe(postCSS([ autoprefixer(autoprefixerSettings) ]))
        .pipe(concat(`${filename}.css`));

    if (isProduction) {
        css.pipe(cleanCSS());
    }

    css.pipe(gulp.dest("public/css/"));

    if (!isProduction) {
        css.pipe(browserSync.stream({ match: `**/${filename}.css` }));
    }

    return css;
}

// Process css
function processCSS(outputFilename, inputFiles) {
    const css = orderedGulpSources(inputFiles)
        .pipe(plumber(handleError))
        .pipe(postCSS([ autoprefixer(autoprefixerSettings) ]))
        .pipe(concat(`${outputFilename}.css`));

    if (isProduction) {
        css.pipe(cleanCSS());
    }

    return css.pipe(gulp.dest("public/css/"));
}

// Process javascript
function processJavaScript(outputFilename, inputFiles, es6) {
    const javascript = orderedGulpSources(inputFiles)
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

// Update the version string
function updateVersion() {
    crypto.randomBytes(16, (err, buf) => {
        if (err) { throw err; }

        return fs.writeFile("storage/app/__version.txt", buf.toString("hex"), (err) => {
            if (err) { throw err; }
        });
    });
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
    return processJavaScript("app", jsPublic, true);
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
    gulp.src(fontPaths, { encoding: false })
        .pipe(plumber(handleError))
        .pipe(gulp.dest("public/fonts/"));

    done();
});

// Task to update the cache-bust version
gulp.task("version", (done) => {
    updateVersion();
    done();
});

// Task to watch files and run respective tasks when changes occur
gulp.task("watch", () => {
    const browserSyncReload = (done) => {
        updateVersion();
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
    gulp.watch("resources/js/**/*.js", gulp.series(gulp.parallel("js-public", "js-dashboard"), browserSyncReload));
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
    "fonts",
    "version"
));
