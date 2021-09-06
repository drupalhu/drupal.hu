const gulp = require('gulp');
const sass = require('gulp-sass')(require('node-sass'));
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
taskLintSass.description = 'Lint *.scss and *.sass files';

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
taskBuildSass.description = 'Compiles *.scss files into *.css files.'

function taskServe() {
  browserSync.init(config.browserSync.options);

  gulp
    .watch(
      config.paths.sass.src,
      taskBuildSass,
    )
    .on('change', browserSync.reload)
}
taskServe.description = 'File watcher and browser sync.'

const taskLint = gulp.parallel(taskLintSass);
taskLint.description = 'Runs all lint:* tasks.';

const taskBuild = gulp.parallel(taskBuildSass);
taskBuild.description = 'Runs all build:* tasks.';

gulp.task('lint', taskLint);
gulp.task('lint:sass', taskLintSass);
gulp.task('build', taskBuild);
gulp.task('build:sass', taskBuildSass);
gulp.task('serve', taskServe);
