<?php
/**
 * @file
 * drupalhu_slideshow.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function drupalhu_slideshow_ctools_plugin_api($module = NULL, $api = NULL) {
  if ($module == "strongarm" && $api == "strongarm") {
    return array("version" => "1");
  }
}

/**
 * Implements hook_views_api().
 */
function drupalhu_slideshow_views_api($module = NULL, $api = NULL) {
  return array("api" => "3.0");
}

/**
 * Implements hook_image_default_styles().
 */
function drupalhu_slideshow_image_default_styles() {
  $styles = array();

  // Exported image style: slideshow.
  $styles['slideshow'] = array(
    'effects' => array(
      2 => array(
        'name' => 'image_scale_and_crop',
        'data' => array(
          'width' => 960,
          'height' => 300,
        ),
        'weight' => 1,
      ),
    ),
    'label' => 'slideshow',
  );

  return $styles;
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
  drupal_alter('node_info', $items);
  return $items;
}
