
var gulp = require('gulp');
var sassLint = require('gulp-sass-lint');
require('gulp-frontend-tasks')(gulp);

gulp.task(
  'sass:lint',
  function () {
    return gulp
      .src(['scss/**/*.scss', 'sass/**/*.scss'])
      .pipe(sassLint({
        files: {ignore: 'scss/base/_normalize.scss'}
      }))
      .pipe(sassLint.format())
      .pipe(sassLint.failOnError());
  }
);
