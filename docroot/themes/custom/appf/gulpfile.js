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

const defaultConfig = {
  sassBuild: {
    options: {},
  },
  sassLint: {
    options: {
      configFile: '.sass-lint.yml',
    },
  },
  browserSync: {
    options: {
      proxy: 'http://drupalhu.localhost',
    },
  },
};

merge(
  config,
  defaultConfig,
  require('./gulp.config')
);

if (fs.existsSync('./gulp.config.local.json')) {
  merge(
    config,
    require('./gulp.config.local')
  );
}

const paths = {
  scss: {
    src:   './css/style.scss',
    dest:  './css',
    watch: './css/**/*.scss',
  },
  js:   {
    bootstrap: './node_modules/bootstrap/dist/js/bootstrap.min.js',
    jquery:    './node_modules/jquery/dist/jquery.min.js',
    popper:    './node_modules/popper.js/dist/umd/popper.min.js',
    dest:      './js'
  }
};

function taskLintSass() {
  return gulp
    .src([
      'css/**/*.scss',
    ])
    .pipe(sassLint(config.sassLint.options))
    .pipe(sassLint.format())
    .pipe(sassLint.failOnError());
}

function taskBuildSass() {
  return gulp
    .src([
      paths.scss.src,
    ])
    .pipe(sourcemaps.init())
    .pipe(
      sass(config.sassBuild.options)
        .on('error', sass.logError)
    )
    .pipe(postcss([
      autoPrefixer({
        browsers: [
          'Chrome >= 70',
          'Firefox >= 60',
          'Edge >= 12',
          'Explorer >= 10',
          'iOS >= 8',
          'Safari >= 8',
          'Android 2.3',
          'Android >= 4',
          'Opera >= 12',
        ]
      })
    ]))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(paths.scss.dest))
}

function taskBuildJs() {
  return gulp
    .src([
      paths.js.bootstrap,
      paths.js.jquery,
      paths.js.popper
    ])
    .pipe(gulp.dest(paths.js.dest))
    .pipe(browserSync.stream())
}

function taskServe() {
  browserSync.init(config.browserSync.options);

  gulp
    .watch(
      [
        paths.scss.watch,
      ],
      taskBuildSass()
    )
    .on('change', browserSync.reload)
}

gulp.task('lint:sass', taskLintSass);

gulp.task('build', gulp.parallel(taskBuildSass, taskBuildJs));

gulp.task('build:sass', taskBuildSass);

gulp.task('build:js', taskBuildJs);

gulp.task('serve', taskServe);
