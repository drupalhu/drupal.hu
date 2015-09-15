<?php
/**
 * @file
 * template.php
 */

/**
 * Implements hook_preprocess_html().
 */
function drupalhu_theme_preprocess_html(&$variables) {
  // Path to theme.
  $variables['path_to_theme'] = url(drupal_get_path('theme', 'drupalhu_theme', array('absolute' => TRUE)));

  drupal_add_css(
    path_to_theme() . '/css/style-ie.css',
    array(
      'group' => CSS_THEME,
      'weight' => 999,
      'browsers' => array(
        'IE' => 'lt IE 9',
        '!IE' => FALSE,
      ),
    )
  );
}
