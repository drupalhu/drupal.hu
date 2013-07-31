<?php
/**
 * @file
 * tile_view_mode.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function tile_view_mode_ctools_plugin_api() {
  list($module, $api) = func_get_args();
  if ($module == "strongarm" && $api == "strongarm") {
    return array("version" => "1");
  }
}
