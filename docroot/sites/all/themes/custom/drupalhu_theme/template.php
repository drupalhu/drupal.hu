<?php
/**
 * @file
 * template.php
 */


// Include .inc files.
_drupalhu_theme_include_inc_files();

/**
 * Include all .inc files from includes directory.
 */
function _drupalhu_theme_include_inc_files() {
  $theme_path = drupal_get_path('theme', 'drupalhu_theme');
  $files = file_scan_directory($theme_path . '/includes', '/\.inc$/');

  foreach ($files as $path) {
    $is_module = 'modules' == basename(dirname($path->uri));
    // Auto include file if not in modules dir.
    // Include files from modules dir if module is exists.
    if (
      !$is_module
      || ($is_module && module_exists($path->name))
    ) {
      require_once $path->uri;
    }
  }
}

/**
 * Get theme hooks.
 */
function _drupalhu_theme_hooks($hook) {
  $hooks = &drupal_static(__FUNCTION__, array());
  if (!isset($hooks[$hook])) {
    $hooks[$hook] = array();
    $prefix = "_drupalhu_theme_{$hook}__";
    $all_functions = get_defined_functions();
    foreach ($all_functions['user'] as $func) {
      if (0 !== strpos($func, $prefix)) {
        continue;
      }
      $hooks[$hook][] = $func;
    }
  }

  return $hooks[$hook];
}

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
