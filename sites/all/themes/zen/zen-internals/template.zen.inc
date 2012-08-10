<?php
/**
 * @file
 * Add stylesheets that are only needed when Zen is the enabled theme.
 *
 * Don't do something this dumb in your sub-theme. Stylesheets should be added
 * using your sub-theme's .info file. If you desire styles that apply
 * conditionally, you can conditionally add a "body class" in the
 * preprocess_page function. For an example, see how wireframe styling is
 * handled in zen_preprocess_html() and wireframes.css.
 */

/**
 * If the user is silly and enables Zen as the theme, manually add some stylesheets.
 */
function _zen_preprocess_html(&$variables, $hook) {
  $directory = drupal_get_path('theme', 'zen') . '/zen-internals/css/';

  // Add Zen's stylesheets manually instead of via its .info file. We do not
  // want the stylesheets to be inherited from Zen since it becomes impossible
  // to re-order the stylesheets in the sub-theme.
  $stylesheets = array(
    'normalize.css',
    'wireframes.css',
    'layouts/responsive-sidebars.css',
    'page-backgrounds.css',
    'tabs.css',
    'pages.css',
    'blocks.css',
    'navigation.css',
    'views-styles.css',
    'nodes.css',
    'comments.css',
    'forms.css',
    'fields.css',
    'print.css',
  );
  if (theme_get_setting('zen_layout') == 'zen-fixed-width') {
    // Replace the fixed-width.css file.
    $stylesheets[2] = 'layouts/fixed-width.css';
  }
  foreach ($stylesheets as $stylesheet) {
    drupal_add_css($directory . $stylesheet, array('group' => CSS_THEME, 'every_page' => TRUE));
  }
}
