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
