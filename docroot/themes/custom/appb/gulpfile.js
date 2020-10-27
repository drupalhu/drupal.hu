const gulp = require('gulp');
const sass = require('gulp-sass');
const sassLint = require('gulp-sass-lint');
const sourcemaps = require('gulp-sourcemaps');
const postcss = require('gulp-postcss');
const autoPrefixer = require('autoprefixer');
const browserSync = require('browser-sync').create();
const config = {};
const fs = require('fs');
const merge = require('lodash/merge');

merge(
  config,
  fs.existsSync('./gulp.config.json') ? require('./gulp.config') : {},
  fs.existsSync('./gulp.config.local.json') ? require('./gulp.config.local') : {},
);

function taskLintSass() {
  return gulp
    .src(config.paths.sass.src)
    .pipe(sassLint(config.sassLint))
    .pipe(sassLint.format())
    .pipe(sassLint.failOnError());
}

function taskBuildSass() {
  return gulp
    .src(config.paths.sass.src)
    .pipe(sourcemaps.init())
    .pipe(
      sass(config.sass)
        .on('error', sass.logError)
    )
    .pipe(postcss(
      [
        autoPrefixer(config.autoprefixer),
      ]
    ))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(config.paths.sass.dest))
}

function taskServe() {
  browserSync.init(config.browserSync.options);

  gulp
    .watch(
      config.paths.sass.src,
      taskBuildSass,
    )
    .on('change', browserSync.reload)
}

gulp.task('lint', gulp.parallel(taskLintSass));

gulp.task('lint:sass', taskLintSass);

gulp.task('build', gulp.parallel(taskBuildSass));

gulp.task('build:sass', taskBuildSass);

gulp.task('serve', taskServe);
