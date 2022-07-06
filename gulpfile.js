// Core packages
const gulp = require("gulp"),
    minimist = require("minimist"),
    log = require("fancy-log"),
    plumber = require("gulp-plumber"),
    concat = require("gulp-concat"),
    fs = require("fs"),
    crypto = require("crypto");

// Sass and CSS packages
const { sass } = require("@mr-hope/gulp-sass"),
    sassGlob = require("gulp-sass-glob"),
    postCSS = require("gulp-postcss"),
    autoprefixer = require("autoprefixer"),
    cleanCSS = require("gulp-clean-css");

// Javascript packages
const babel = require("gulp-babel"),
    stripDebug = require("gulp-strip-debug"),
    uglify = require("gulp-uglify-es").default;

// Vue packages
const webpack = require("webpack"),
    terserWebpackPlugin = require("terser-webpack-plugin"),
    { VueLoaderPlugin } = require("vue-loader"),
    path = require("path");

// Determine if gulp has been run with --production
const isProduction = minimist(process.argv.slice(2)).production !== undefined;

// Declare plugin settings
const sassOutputStyle = isProduction ? "compressed" : "expanded",
    sassPaths = "node_modules",
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
        .pipe(sass({ outputStyle: sassOutputStyle }))
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
function processVue(outputFilename, inputFile, done) {
    webpack({
        mode: isProduction ? "production" : "development",
        entry: [ `./${inputFile}` ],
        output: { path: path.resolve(__dirname, "public/js"), filename: `${outputFilename}.js` },
        devtool: false,

        performance: {
            maxEntrypointSize: 500000,
            maxAssetSize: 500000
        },

        resolve: {
            alias: {
                vue$: "vue/dist/vue.esm-bundler.js",
                vuex$: "vuex/dist/vuex.esm-bundler.js",
                pages: path.resolve(__dirname, "resources/components/pages"),
                sections: path.resolve(__dirname, "resources/components/sections"),
                partials: path.resolve(__dirname, "resources/components/partials"),
                mixins: path.resolve(__dirname, "resources/js/mixins"),
                imports: path.resolve(__dirname, "resources/js/imports")
            }
        },

        module: {
            rules: [
                {
                    test: /\.vue$/,
                    loader: "vue-loader",
                    options: { presets: [ [ "@babel/preset-env" ] ] }
                },

                {
                    test: /\.js$/,
                    loader: "babel-loader",
                    options: { presets: [ [ "@babel/preset-env" ] ] }
                }
            ]
        },

        plugins: [
            new webpack.DefinePlugin({ __VUE_OPTIONS_API__: true, __VUE_PROD_DEVTOOLS__: false }),
            new VueLoaderPlugin()
        ],

        optimization: {
            minimizer: [
                new terserWebpackPlugin({
                    extractComments: false,

                    terserOptions: {
                        format: { comments: false },
                        compress: { drop_console: isProduction }
                    }
                })
            ]
        }
    }, (err, stats) => {
        let statsJson;

        if (err) {
            log.error(err.stack || err);

            if (err.details) {
                log.error(err.details);
            }
        } else if (stats.hasWarnings() || stats.hasErrors()) {
            statsString = stats.toString("errors-only", {
                colors: true,
                modules: false,
                children: false,
                chunks: false,
                chunkModules: false
            });

            log.error(statsString);
        }

        done();
    });
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
gulp.task("js-public", (done) => {
    return processVue("app", jsPublic, done);
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
    gulp.watch([ "resources/js/**/app.js", "resources/js/mixins/**/*.js", "resources/js/imports/**/*.js", "resources/components/**/*.vue" ], gulp.series("js-public", browserSyncReload));
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
    "fonts",
    "version"
));
