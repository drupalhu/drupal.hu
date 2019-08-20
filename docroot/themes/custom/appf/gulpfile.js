const gulp = require('gulp');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const postcss = require('gulp-postcss');
const autoPrefixer = require('autoprefixer');
const browserSync = require('browser-sync').create();
const config = {};
const fs = require('fs');
const merge = require('lodash/merge');

const defaultConfig = {
  sass:        {
    options: {},
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

function build() {
  return gulp
    .src([
      paths.scss.src,
    ])
    .pipe(sourcemaps.init())
    .pipe(
      sass(config.sass.options)
        .on('error', sass.logError)
    )
    .pipe(postcss([
      autoPrefixer({
        browsers: [
          'Chrome >= 35',
          'Firefox >= 38',
          'Edge >= 12',
          'Explorer >= 10',
          'iOS >= 8',
          'Safari >= 8',
          'Android 2.3',
          'Android >= 4',
          'Opera >= 12'
        ]
      })
    ]))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(paths.scss.dest))
}

function js() {
  return gulp
    .src([
      paths.js.bootstrap,
      paths.js.jquery,
      paths.js.popper
    ])
    .pipe(gulp.dest(paths.js.dest))
    .pipe(browserSync.stream())
}

function serve() {
  browserSync.init(config.browserSync.options);

  gulp
    .watch(
      [
        paths.scss.watch,
      ],
      build
    )
    .on('change', browserSync.reload)
}

exports.build = build;
exports.js = js;
exports.serve = serve;

exports.default = build;
