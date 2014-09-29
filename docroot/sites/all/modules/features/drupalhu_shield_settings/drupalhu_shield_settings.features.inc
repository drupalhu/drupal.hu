<?php
/**
 * @file
 * drupalhu_shield_settings.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function drupalhu_shield_settings_ctools_plugin_api() {
  list($module, $api) = func_get_args();
  if ($module == "strongarm" && $api == "strongarm") {
    return array("version" => "1");
  }
}
