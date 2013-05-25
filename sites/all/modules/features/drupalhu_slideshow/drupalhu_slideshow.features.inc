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
 * Implements hook_image_default_styles().
 */
function drupalhu_slideshow_image_default_styles() {
  $styles = array();

  // Exported image style: slideshow.
  $styles['slideshow'] = array(
    'name' => 'slideshow',
    'effects' => array(
      2 => array(
        'label' => 'Arányos átméretezés és méretre vágás',
        'help' => 'Az arányos átméretezés és kivágás megtartja a képarányt és azt követően kivágja a képet a hosszabbik oldal alapján. Ez a legáltalánosabban használt hatás négyzet alakú bélyegképek létrehozására anélkül, hogy a kép el lenne nyújtva.',
        'effect callback' => 'image_scale_and_crop_effect',
        'dimensions callback' => 'image_resize_dimensions',
        'form callback' => 'image_resize_form',
        'summary theme' => 'image_resize_summary',
        'module' => 'image',
        'name' => 'image_scale_and_crop',
        'data' => array(
          'width' => '960',
          'height' => '300',
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
