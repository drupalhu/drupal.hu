<?php
/**
 * @file
 * dhu_core.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function dhu_core_ctools_plugin_api($module = NULL, $api = NULL) {
  if ($module == "openlayers" && $api == "openlayers_maps") {
    return array("version" => "1");
  }
  if ($module == "strongarm" && $api == "strongarm") {
    return array("version" => "1");
  }
}
