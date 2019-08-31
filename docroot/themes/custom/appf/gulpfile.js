const gulp = require('gulp');
const gulpSassLint = require('gulp-sass-lint');
const exec = require('child_process').exec;
const fs = require('fs');
const merge = require('lodash/merge');
const process = require('process');

/**
 * @typedef {object} BrowserSyncOptions
 * @property {string} proxy
 * @property {boolean} stream
 * @property {string[]} files
 */

/**
 * @typedef {object} BrowserSyncConfig
 * @property {BrowserSyncOptions} options
 */

/**
 * @typedef {object} SassLintFiles
 * @property {string} include
 * @property {string} exclude
 */

/**
 * @typedef {object} SassLintConfigOptions
 * @property {SassLintFiles} files
 * @property {object} rules
 * @property {string} configFile
 * @property {string} formatter
 */

/**
 * @typedef {object} SassLintConfig
 * @property {SassLintConfigOptions} options
 */

/**
 * @typedef {Object} AppConfig
 * @property {string} nodeEnv
 * @property {BrowserSyncConfig} browserSync
 * @property {SassLintConfig} sassLint
 */

/**
 * @type {AppConfig}
 */
const config = {};
merge(config, require('./gulp.config'));
if (fs.existsSync('./gulp.config.local.json')) {
  merge(config, require('./gulp.config.local'));
}

function taskLintSass() {
  return gulp
    .src([
      'src/**/*.scss',
    ])
    .pipe(gulpSassLint(config.sassLint.options))
    .pipe(gulpSassLint.format())
    .pipe(gulpSassLint.failOnError());
}

function taskBuildWebpack(cb) {
  const command = [
    './node_modules/.bin/webpack',
    '--progress',
    '--hide-modules',
    '--config=./node_modules/laravel-mix/setup/webpack.config.js',
  ];

  if ( process.env.hasOwnProperty('NODE_ENV') === false) {
    command.unshift('NODE_ENV=' + config.nodeEnv);
  }

  exec(
    command.join(' '),
    function (err, stdout, stderr) {
      console.log(stdout);
      console.log(stderr);
      cb(err);
  });
}

gulp.task('lint', gulp.parallel(taskLintSass));
gulp.task('lint:sass', taskLintSass);

gulp.task('build', gulp.parallel(taskBuildWebpack));
gulp.task('build:webpack', taskBuildWebpack);
