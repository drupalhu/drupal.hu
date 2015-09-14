<?php
/**
 * @file
 * admin-menu-links.vars.php
 */

/**
 * Implements hook_preprocess_admin_menu_links().
 */
function bootstrap_preprocess_admin_menu_links(&$variables) {
  $elements = &$variables['elements'];
  foreach (element_children($elements) as $child) {
    $elements[$child]['#bootstrap_ignore_pre_render'] = TRUE;
    $elements[$child]['#bootstrap_ignore_process'] = TRUE;
  }
}
