require('dotenv').config();
const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const autoprefixer = require('gulp-autoprefixer');
const sourcemaps = require('gulp-sourcemaps');
const cleanCSS = require('gulp-clean-css');
const browserSync = require('browser-sync').create();
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');

// Paths
const paths = {
    styles: {
        src: 'assets/src/sass/**/*.scss',
        dest: 'assets/dist/css'
    },
    scripts: {
        src: 'assets/src/js/**/*.js',
        dest: 'assets/dist/js'
    }
};

// Initialize browserSync
function browserSyncInit(done) {
    browserSync.init({
        proxy: process.env.PROXY_URL || "localhost", // Fallback to localhost if not set
        notify: false
    });
    done();
}

// Compile SCSS
function styles() {
    return gulp.src(paths.styles.src)
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(cleanCSS())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(paths.styles.dest))
        .pipe(browserSync.stream());
}

// Concatenate and minify JavaScript
function scripts() {
    return gulp.src(paths.scripts.src)
        .pipe(sourcemaps.init())
        .pipe(concat('main.js'))
        .pipe(uglify())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(paths.scripts.dest))
        .pipe(browserSync.stream());
}

// Watch files (SCSS only)
function watch() {
    gulp.watch(paths.styles.src, styles);
}

// Watch files with browserSync
function watchFiles() {
    gulp.watch(paths.styles.src, styles);
    gulp.watch(paths.scripts.src, scripts);
    gulp.watch('**/*.php').on('change', browserSync.reload);
}

// Define tasks
exports.styles = styles;
exports.scripts = scripts;
exports.watch = watch;
exports.build = gulp.series(styles, scripts);
exports.default = gulp.series(styles, scripts, browserSyncInit, watchFiles);
