<?php
/**
 * @file
 * drupalhu_slideshow.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function drupalhu_slideshow_ctools_plugin_api() {
  list($module, $api) = func_get_args();
  if ($module == "strongarm" && $api == "strongarm") {
    return array("version" => "1");
  }
}

/**
 * Implements hook_views_api().
 */
function drupalhu_slideshow_views_api() {
  return array("version" => "3.0");
}

/**
 * Implements hook_node_info().
 */
function drupalhu_slideshow_node_info() {
  $items = array(
    'slideshow_item' => array(
      'name' => t('Slideshow item'),
      'base' => 'node_content',
      'description' => t('Slideshow item show in site frontpage with image and description field.'),
      'has_title' => '1',
      'title_label' => t('Main title'),
      'help' => '',
    ),
  );
  return $items;
}
