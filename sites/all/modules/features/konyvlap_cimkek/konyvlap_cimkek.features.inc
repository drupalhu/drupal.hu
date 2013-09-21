<?php
/**
 * @file
 * konyvlap_cimkek.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function konyvlap_cimkek_ctools_plugin_api($module = NULL, $api = NULL) {
  if ($module == "field_group" && $api == "field_group") {
    return array("version" => "1");
  }
  if ($module == "strongarm" && $api == "strongarm") {
    return array("version" => "1");
  }
}

/**
 * Implements hook_views_api().
 */
function konyvlap_cimkek_views_api($module = NULL, $api = NULL) {
  return array("api" => "3.0");
}

/**
 * Implements hook_node_info().
 */
function konyvlap_cimkek_node_info() {
  $items = array(
    'book' => array(
      'name' => t('Könyv lap'),
      'base' => 'node_content',
      'description' => t('A Drupal kézikönybe kerülő lap.'),
      'has_title' => '1',
      'title_label' => t('Cím'),
      'help' => '',
    ),
  );
  return $items;
}
