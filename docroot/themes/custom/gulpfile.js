
module.exports = function(gulp, nodeModulesDir) {
  const sass = require(nodeModulesDir + '/gulp-sass');
  const sourceMaps = require(nodeModulesDir + '/gulp-sourcemaps');
  const styleLint = require(nodeModulesDir + '/gulp-stylelint');

  const sassOptions = {
    outputStyle: 'expanded',
    sourceComments: true,
  };

  gulp.task(
    'sass',
    () => gulp
      .src('./css/**/*.scss')
      .pipe(sourceMaps.init())
      .pipe(sass(sassOptions).on('error', sass.logError))
      .pipe(sourceMaps.write())
      .pipe(gulp.dest('./css')),
  );

  gulp.task(
    'sass:watch',
    () => gulp
      .watch('./css/**/*.scss', ['sass']),
  );

  gulp.task(
    'sass:lint',
    () => gulp
      .src('css/**/*.scss')
      .pipe(styleLint({
        configFile: '../.stylelintrc.yml',
        reporters: [
          {
            formatter: 'string',
            console: true,
          },
        ],
      })),
  );

  return gulp;
};
