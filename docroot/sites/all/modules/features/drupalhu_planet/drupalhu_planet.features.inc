<?php
/**
 * @file
 * drupalhu_planet.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function drupalhu_planet_ctools_plugin_api($module = NULL, $api = NULL) {
  if ($module == "feeds" && $api == "feeds_importer_default") {
    return array("version" => "1");
  }
  if ($module == "strongarm" && $api == "strongarm") {
    return array("version" => "1");
  }
}

/**
 * Implements hook_views_api().
 */
function drupalhu_planet_views_api($module = NULL, $api = NULL) {
  return array("api" => "3.0");
}

/**
 * Implements hook_image_default_styles().
 */
function drupalhu_planet_image_default_styles() {
  $styles = array();

  // Exported image style: planet_user_picture.
  $styles['planet_user_picture'] = array(
    'effects' => array(
      3 => array(
        'name' => 'image_scale_and_crop',
        'data' => array(
          'width' => 55,
          'height' => 55,
        ),
        'weight' => 1,
      ),
    ),
    'label' => 'planet_user_picture',
  );

  return $styles;
}

/**
 * Implements hook_node_info().
 */
function drupalhu_planet_node_info() {
  $items = array(
    'feed' => array(
      'name' => t('Hírcsatorna'),
      'base' => 'node_content',
      'description' => t('Az ezen hírcsatornán beérkező elemek tartalmak lesznek a webhelyen.'),
      'has_title' => '1',
      'title_label' => t('Cím'),
      'help' => '',
    ),
    'feed_item' => array(
      'name' => t('Aggregált hír'),
      'base' => 'node_content',
      'description' => t('Hírcsatornából aggregált hír.'),
      'has_title' => '1',
      'title_label' => t('Cím'),
      'help' => '',
    ),
  );
  drupal_alter('node_info', $items);
  return $items;
}
