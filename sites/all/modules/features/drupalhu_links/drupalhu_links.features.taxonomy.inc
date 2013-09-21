<?php
/**
 * @file
 * drupalhu_links.features.taxonomy.inc
 */

/**
 * Implements hook_taxonomy_default_vocabularies().
 */
function drupalhu_links_taxonomy_default_vocabularies() {
  return array(
    'vocabulary_7' => array(
      'name' => 'Linkek',
      'machine_name' => 'vocabulary_7',
      'description' => 'Magyar Drupal webhelyek bemutatója',
      'hierarchy' => 0,
      'module' => 'taxonomy',
      'weight' => 0,
    ),
  );
}
