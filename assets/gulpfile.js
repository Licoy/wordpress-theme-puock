const gulp = require('gulp')
const babel = require('gulp-babel');
const less = require('gulp-less');
const rename = require('gulp-rename');
const uglify = require('gulp-uglify');
const plumber = require('gulp-plumber');
const concat = require('gulp-concat');

const _core_script = "js/core/*.js"
const _libs_script = "js/libs/*.js"
const _less = "css/*.less"

gulp.task('script', function () {
    return gulp.src(_core_script)
        .pipe(plumber())
        .pipe(rename({suffix: '.min'}))
        .pipe(babel())
        .pipe(uglify())
        .pipe(gulp.dest('dist'))
})

gulp.task('w', function () {
    gulp.watch(_core_script, gulp.series('script'))
})