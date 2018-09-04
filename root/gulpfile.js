var nic = 'html';

// gulp
var gulp = require('gulp');
// webpack
var webpack = require('gulp-webpack');
var webpackConfig = require('./webpack.config.js');
var del = require('del');
// sass
var sass = require('gulp-sass');

// webpack
var TS_SRC = './ts/*.ts';
var JS_DEST = nic+'/js/dest/';

gulp.task('clean', function() {
    del([JS_DEST]);
});

gulp.task('webpack', function () {
    return gulp.src([TS_SRC])
        .pipe(webpack(webpackConfig))
        .pipe(gulp.dest(JS_DEST));
});


// sass
gulp.task('sass', function() {
    return gulp.src([
        nic+'/css/scss/*.scss'
    ])
    .pipe(sass({outputStyle: 'expanded'}).on('error', sass.logError))
//    .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
    .pipe(gulp.dest(nic+'/css/dest'));
});


// watch
gulp.task('watch', function() {
    gulp.watch(TS_SRC, ['webpack']);
    gulp.watch([
        nic+'/css/scss/*.scss',
        nic+'/css/scss/admin/*.scss',
        nic+'/css/scss/common/*.scss',
        nic+'/css/scss/styles/*.scss',
    ], ['sass']);
});

gulp.task('default', ['webpack', 'sass', 'watch']);

