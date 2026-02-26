const { src, dest, watch, series } = require('gulp'),
    sass                           = require('gulp-sass')(require('sass')),
    postcss                        = require('gulp-postcss'),
    autoprefixer                   = require('autoprefixer'),
    cssnano                        = require('cssnano'),
    sourcemaps                     = require('gulp-sourcemaps'),
    eslint                         = require('gulp-eslint'),
    rename                         = require('gulp-rename'),
    uglify                         = require('gulp-uglify'),
    config                         = require('./gulpfile-config'),
    imagemin                       = require('gulp-imagemin'),
    log                            = require('fancy-log'),
    babel                          = require('gulp-babel'),
    browserSync                    = require('browser-sync').create();

const paths = {
  styles: {
    src: ["assets/src/sass/**/*.scss"],
    dest: "assets/dist/css",
    maps: "../maps"
  },
  scripts:{
    src: "assets/src/js/**/*.js",
    dest: "assets/dist/js",
    maps: "../maps"
  },
  images:{
    src: "assets/src/images/*",
    dest: "assets/dist/images"
  },
  php: {
    src: "**/*.php"  // Watch all PHP files in the theme
  }
};

/**
 * @task styles
 * Compile files from scss src, run postcss, write sourcemap, send to dest, and refresh browser
 */
const styles = () => src(paths.styles.src)
  .pipe(sourcemaps.init())
  .pipe(sass().on('error', sass.logError))
  .pipe(postcss([autoprefixer(), cssnano()]))
  .pipe(sourcemaps.write(paths.styles.maps))
  .pipe(dest(paths.styles.dest))
  .pipe(browserSync.stream());

/**
 * @task lint
 * Detects errors and potential problems in JavaScript code
 */
const lint = () => src(paths.scripts.src)
  .pipe(eslint(
    {
      useEslintrc: true,
      fix: true
    }
  ))
  .pipe(eslint.format())
  .pipe(eslint.results(results => {
    // Called once for all ESLint results.
      console.log(`Total Results: ${results.length}`);
      console.log(`Total Warnings: ${results.warningCount}`);
      console.log(`Total Errors: ${results.errorCount}`);
  }));

/**
 * @task scripts
 * minify, and send scripts
 * `scripts` depends on `lint`
 */
const scripts = () => src(paths.scripts.src)
  .pipe(sourcemaps.init())
  .pipe(babel())
  .pipe(dest(paths.scripts.dest))
  .pipe(rename({ suffix: '.min' }))
  .pipe(uglify().on('error', (e) => { log(e); }))
  .pipe(sourcemaps.write(paths.scripts.maps))
  .pipe(dest(paths.scripts.dest))
  .pipe(browserSync.stream())
  .on('end', () => { log('Scripts Done!'); });

/**
 * @task minify
 * Minify PNG, JPEG, GIF and SVG images with imagemin
 */
const imageminify = () => src(paths.images.src)
    .pipe(imagemin([
        imagemin.gifsicle({interlaced: true}),
        imagemin.mozjpeg({progressive: true}),
        imagemin.optipng({optimizationLevel: 5}),
        imagemin.svgo({
            plugins: [
                {removeViewBox: true},
                {cleanupIDs: false}
            ]
        })
    ]))
    .pipe(dest(paths.images.dest));

// Add browsersync initialization at the start of the watch task
// We don't have to expose the reload function
// It's currently only useful in other functions
const serve = () => {
    browserSync.init({
        proxy: config.browserSync.proxy
    });

    watch(paths.styles.src, styles);
    watch(paths.scripts.src, series(lint, scripts));
    watch(paths.php.src).on('change', browserSync.reload); // Add PHP file watching
    log('Watch function completed');
}

/**
 * Default test task, running just `gulp` will
 * compile Sass files, launch Browsersync & watch files.
 */
exports.default = (cb) => {
    cb();
    serve();
};

// Function are exported to be public and can be run with the `gulp` command.
exports.serve       = serve;
exports.imageminify = imageminify;
exports.styles      = styles;
exports.lint        = lint;
exports.scripts     = series(lint, scripts);
exports.watch       = series(styles, series(lint, scripts), serve);
exports.build       = series(styles, series(lint, scripts), imageminify);