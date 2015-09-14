<?php
/**
 * @file
 * breadcrumb.vars.php
 */

/**
 * Implements hook_preprocess_breadcrumb().
 */
function bootstrap_preprocess_breadcrumb(&$variables) {
  $breadcrumb = &$variables['breadcrumb'];

  // Optionally get rid of the homepage link.
  $show_breadcrumb_home = bootstrap_setting('breadcrumb_home');
  if (!$show_breadcrumb_home) {
    array_shift($breadcrumb);
  }

  if (bootstrap_setting('breadcrumb_title') && !empty($breadcrumb)) {
    $item = menu_get_item();

    $page_title = !empty($item['tab_parent']) ? check_plain($item['title']) : drupal_get_title();
    if (!empty($page_title)) {
      $breadcrumb[] = array(
        // If we are on a non-default tab, use the tab's title.
        'data' => $page_title,
        'class' => array('active'),
      );
    }
  }
}
