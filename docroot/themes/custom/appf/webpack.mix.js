const fs = require('fs');
const merge = require('lodash/merge');

/**
 * @type {AppConfig}
 */
const config = {};
merge(config, require('./gulp.config'));
if (fs.existsSync('./gulp.config.local.json')) {
  merge(config, require('./gulp.config.local'));
}

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your application. See https://github.com/JeffreyWay/laravel-mix.
 |
 */
const mix = require('laravel-mix');

mix
  .setPublicPath('assets')
  .disableNotifications()
  .options({
    processCssUrls: false
  });

mix.browserSync(config.browserSync.options);

mix.sass('src/sass/appf.style.scss', 'css');
mix.sass('src/sass/component/facet/widget/appf.facets.widget.checkbox.layout.scss', 'css');
mix.sass('src/sass/component/facet/widget/appf.facets.widget.checkbox.theme.scss', 'css');

mix.js('src/js/appf.script.js', 'js');
