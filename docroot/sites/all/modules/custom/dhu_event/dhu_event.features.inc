<?php
/**
 * @file
 * dhu_event.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function dhu_event_ctools_plugin_api($module = NULL, $api = NULL) {
  if ($module == "strongarm" && $api == "strongarm") {
    return array("version" => "1");
  }
}

/**
 * Implements hook_node_info().
 */
function dhu_event_node_info() {
  $items = array(
    'esemeny' => array(
      'name' => t('Esemény'),
      'base' => 'node_content',
      'description' => t('Események felviteléhez használható tartalomtípus, a honlap elkülönített részén jelenik meg.'),
      'has_title' => '1',
      'title_label' => t('Cím'),
      'help' => '',
    ),
  );
  drupal_alter('node_info', $items);
  return $items;
}
