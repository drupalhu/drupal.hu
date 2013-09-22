<?php
/**
 * @file
 * drupal_hu_content_types.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function drupal_hu_content_types_ctools_plugin_api($module = NULL, $api = NULL) {
  if ($module == "strongarm" && $api == "strongarm") {
    return array("version" => "1");
  }
}

/**
 * Implements hook_node_info().
 */
function drupal_hu_content_types_node_info() {
  $items = array(
    'esemeny' => array(
      'name' => t('Esemény'),
      'base' => 'node_content',
      'description' => t('Események felviteléhez használható tartalomtípus, a honlap elkülönített részén jelenik meg.'),
      'has_title' => '1',
      'title_label' => t('Cím'),
      'help' => '',
    ),
    'faq' => array(
      'name' => t('Gyakran Ismételt Kérdés'),
      'base' => 'node_content',
      'description' => t('Ha szeretnénk egy új kérdés-válasz párt felvenni a Gyakran Ismételt Kérdések közé, akkor egy ilyen node kitöltésével tehetjük ezt meg. A kérdés a node címe a választ a node törzse.'),
      'has_title' => '1',
      'title_label' => t('Kérdés'),
      'help' => '',
    ),
    'news' => array(
      'name' => t('Hír'),
      'base' => 'node_content',
      'description' => t('A honlapra kerülő hír.'),
      'has_title' => '1',
      'title_label' => t('Cím'),
      'help' => '',
    ),
    'note' => array(
      'name' => t('Feljegyzés'),
      'base' => 'node_content',
      'description' => t('A Drupal.hu belső feljegyzése, nem közzétételre szánt tartalom.'),
      'has_title' => '1',
      'title_label' => t('Cím'),
      'help' => '',
    ),
    'page' => array(
      'name' => t('Oldal'),
      'base' => 'node_content',
      'description' => t('Statikus tartalmú oldalak.'),
      'has_title' => '1',
      'title_label' => t('Cím'),
      'help' => '',
    ),
    'webform' => array(
      'name' => t('Űrlap'),
      'base' => 'node_content',
      'description' => t('Create a new form or questionnaire accessible to users. Submission results and statistics are recorded and accessible to privileged users.'),
      'has_title' => '1',
      'title_label' => t('Cím'),
      'help' => '',
    ),
  );
  return $items;
}
