<?php

/**
 * @file
 * drupalhu_staging.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function drupalhu_staging_ctools_plugin_api($module = NULL, $api = NULL) {
  if ($module == "strongarm" && $api == "strongarm") {
    return array("version" => "1");
  }
}
