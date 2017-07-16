'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var autoprefixer = require('gulp-autoprefixer');
var cssnano = require('gulp-cssnano');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglifyjs');
var browserSync = require('browser-sync');
var reload = browserSync.reload;
var php = require('gulp-connect-php');

// settings
var paths = {
    sass: './src/scss/**/*.scss',
    js: './src/js/**/*.js'
};

/**
 * compile scss, autoprefix and minify it
 * create sourcemaps
 */
gulp.task('sass', function () {
    return gulp.src(paths.sass)
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({ cascade: true }))
        .pipe(sourcemaps.init())
        .pipe(cssnano())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('./public/css'))
    ;
});

/**
 * compile concatenated, uglified js
 */
gulp.task('js', function () {
    return gulp.src(paths.js)
        .pipe(uglify())
        .pipe(concat('main.js'))
        .pipe(gulp.dest('./public/js'))
    ;
});

/**
 * watch scss, js and php files
 */
gulp.task('watch', function () {
    gulp.watch(paths.sass, ['sass']);
    gulp.watch(paths.js, ['js']);
    // we only reload on template changes to prevent unwanted reloads (e.g. form submits)
    gulp.watch(['src/templates/**/*.php'], reload);
});

/**
 * start local php server
 * @see https://fettblog.eu/php-browsersync-grunt-gulp/
 */
gulp.task('php', function() {
    php.server({ base: 'public', port: 8010, keepalive: true});
});

/**
 * watch files, start builtin php server and serve with browser-sync
 */
gulp.task('serve', ['php', 'watch'], function() {
  browserSync({
    // proxy to our created php server
    proxy: "localhost:8010",
    open: true,
    notify: false,
    // Customise the placement of the snippet
    snippetOptions: {
        rule: {
            match: /<\/body>/i,
            fn: function (snippet, match) {
                return snippet + match;
            }
        }
    }
  });
});

// compile files, serve
gulp.task('default', ['sass', 'js', 'serve']);
