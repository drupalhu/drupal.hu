<?php
/**
 * @file
 * dhu_core.features.taxonomy.inc
 */

/**
 * Implements hook_taxonomy_default_vocabularies().
 */
function dhu_core_taxonomy_default_vocabularies() {
  return array(
    'vocabulary_5' => array(
      'name' => 'Hírek',
      'machine_name' => 'vocabulary_5',
      'description' => '',
      'hierarchy' => 0,
      'module' => 'taxonomy',
      'weight' => 0,
    ),
  );
}
