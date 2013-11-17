<?php
/**
 * @file
 * drupalhu_content_types.features.taxonomy.inc
 */

/**
 * Implements hook_taxonomy_default_vocabularies().
 */
function drupalhu_content_types_taxonomy_default_vocabularies() {
  return array(
    'vocabulary_1' => array(
      'name' => 'Tudásbázis',
      'machine_name' => 'vocabulary_1',
      'description' => 'Kérdés-válasz fórum',
      'hierarchy' => 0,
      'module' => 'forum',
      'weight' => -10,
    ),
    'vocabulary_10' => array(
      'name' => 'Drupal verzió',
      'machine_name' => 'vocabulary_10',
      'description' => '',
      'hierarchy' => 1,
      'module' => 'taxonomy',
      'weight' => 0,
    ),
    'vocabulary_11' => array(
      'name' => 'Melyik modulhoz, modulokhoz kapcsolódik a téma?',
      'machine_name' => 'vocabulary_11',
      'description' => '',
      'hierarchy' => 0,
      'module' => 'taxonomy',
      'weight' => 0,
    ),
    'vocabulary_5' => array(
      'name' => 'Hírek',
      'machine_name' => 'vocabulary_5',
      'description' => '',
      'hierarchy' => 0,
      'module' => 'taxonomy',
      'weight' => 0,
    ),
    'vocabulary_6' => array(
      'name' => 'Kézikönyv',
      'machine_name' => 'vocabulary_6',
      'description' => 'A Drupal kézikönyv lapjai',
      'hierarchy' => 0,
      'module' => 'taxonomy',
      'weight' => 0,
    ),
  );
}
