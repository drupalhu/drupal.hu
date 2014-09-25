<?php
/**
 * @file
 * drupalhu_planet.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function drupalhu_planet_ctools_plugin_api() {
  list($module, $api) = func_get_args();
  if ($module == "feeds" && $api == "feeds_importer_default") {
    return array("version" => "1");
  }
  list($module, $api) = func_get_args();
  if ($module == "strongarm" && $api == "strongarm") {
    return array("version" => "1");
  }
}

/**
 * Implements hook_views_api().
 */
function drupalhu_planet_views_api() {
  return array("version" => "3.0");
}

/**
 * Implements hook_image_default_styles().
 */
function drupalhu_planet_image_default_styles() {
  $styles = array();

  // Exported image style: planet_user_picture.
  $styles['planet_user_picture'] = array(
    'name' => 'planet_user_picture',
    'effects' => array(
      3 => array(
        'label' => 'Arányos átméretezés és méretre vágás',
        'help' => 'Az arányos átméretezés és kivágás megtartja a képarányt és azt követően kivágja a képet a hosszabbik oldal alapján. Ez a legáltalánosabban használt hatás négyzet alakú bélyegképek létrehozására anélkül, hogy a kép el lenne nyújtva.',
        'effect callback' => 'image_scale_and_crop_effect',
        'dimensions callback' => 'image_resize_dimensions',
        'form callback' => 'image_resize_form',
        'summary theme' => 'image_resize_summary',
        'module' => 'image',
        'name' => 'image_scale_and_crop',
        'data' => array(
          'width' => '55',
          'height' => '55',
        ),
        'weight' => '1',
      ),
    ),
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
  return $items;
}
