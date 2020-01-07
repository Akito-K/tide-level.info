var port    = 10089; // docker-composeで指定したport
var scssDir = './scss';
var tsDir   = './ts';
var distDir = './html/dist';

var gulp = require('gulp');
var $ = require('gulp-load-plugins')();
var sass = require('gulp-sass');
var packageImporter = require('node-sass-package-importer');
var webpack = require('webpack-stream');
var del = require('del');
var webpackConfig = require('./webpack.config.js');
var browserSync = require('browser-sync').create();

var scss_sources = [
    scssDir + '/*.scss',
    scssDir + '/*/*.scss',
    scssDir + '/*/*/*.scss'
];
var css_dest = distDir + '/css/';
var ts_sources = [
    tsDir + '/*.ts',
    tsDir + '/*/*.ts',
];
var js_dest = distDir + '/js/';


gulp.task('server', function(){
    browserSync.init({
        proxy: 'localhost:' + port
    });
});

gulp.task('clean', function() {
    del(js_dest);
    del(css_dest);
});

// webpack(ts)
gulp.task('webpack', function () {
    return gulp.src( ts_sources )
        .pipe(
            webpack(webpackConfig)
                .on('error', function(e){
                    this.emit('end');
                })
        )
        .pipe(gulp.dest( js_dest ))
        .pipe(browserSync.stream());
});


// sass
gulp.task('sass', function() {
    return gulp.src( scss_sources )
    .pipe(
        sass(
            {
                importer: packageImporter({
                    extensions: ['.scss', '.css']
                }),
                outputStyle: 'expanded',
//                outputStyle: 'compressed',
            }
        )
        .on('error', sass.logError)
    )
    .pipe($.autoprefixer({
        grid: true,
    }))

    .pipe(gulp.dest( css_dest ))
    .pipe(browserSync.stream());
});


// watch
gulp.task('watch', function() {
    gulp.watch( ts_sources, ['webpack']);
    gulp.watch( scss_sources, ['sass']);
});

gulp.task('default', [
    'server',
    'webpack',
    'sass',
    'watch'
]);

