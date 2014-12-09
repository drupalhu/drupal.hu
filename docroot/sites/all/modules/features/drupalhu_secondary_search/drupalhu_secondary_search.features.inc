<?php
/**
 * @file
 * drupalhu_secondary_search.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function drupalhu_secondary_search_ctools_plugin_api() {
  list($module, $api) = func_get_args();
  if ($module == "page_manager" && $api == "pages_default") {
    return array("version" => "1");
  }
}

/**
 * Implements hook_views_api().
 */
function drupalhu_secondary_search_views_api() {
  return array("version" => "3.0");
}
