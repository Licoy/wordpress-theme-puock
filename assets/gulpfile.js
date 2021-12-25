const gulp = require('gulp')
const babel = require('gulp-babel');
const less = require('gulp-less');
const rename = require('gulp-rename');
const uglify = require('gulp-uglify');
const plumber = require('gulp-plumber');
const concat = require('gulp-concat');
const concatCss = require('gulp-concat-css');
const minifyCSS = require('gulp-minify-css')

const _core_script = "js/*.js"
const _libs_script = "libs/**/*.js"
const _core_style = "style/*.less"
const _libs_style = "libs/**/*.css"

gulp.task('style', function () {
    return gulp.src(_core_style)
        .pipe(plumber())
        .pipe(less({
            compress: true
        }))
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('dist'))
})

gulp.task('lib_style', function () {
    return gulp.src(_libs_style)
        .pipe(concatCss("libs.min.css"))
        .pipe(minifyCSS({
            format: 'keep-breaks',
            semicolonAfterLastProperty: true,
            afterComment: true
        }))
        .pipe(gulp.dest('dist'))
})

gulp.task('lib_script', function () {
    return gulp.src(_libs_script)
        .pipe(concat("libs.min.js"))
        .pipe(uglify({
            output: {
                comments: true,
            }
        }))
        .pipe(gulp.dest('dist'))
})

gulp.task('script', function () {
    return gulp.src(_core_script)
        .pipe(plumber())
        .pipe(rename({suffix: '.min'}))
        .pipe(babel())
        .pipe(uglify())
        .pipe(gulp.dest('dist'))
})

gulp.task('build', gulp.series(
    'style',
    'lib_style',
    'script',
    'lib_script',
))

gulp.task('w', gulp.series('build', function () {
    gulp.watch(_core_style, gulp.series('style'))
    gulp.watch(_core_script, gulp.series('script'))
    gulp.watch(_libs_script, gulp.series('lib_script'))
    gulp.watch(_libs_style, gulp.series('lib_style'))
}))
