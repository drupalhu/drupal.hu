<?php
/**
 * @file
 * drupalhu_common.features.taxonomy.inc
 */

/**
 * Implements hook_taxonomy_default_vocabularies().
 */
function drupalhu_common_taxonomy_default_vocabularies() {
  return array(
    'vocabulary_8' => array(
      'name' => 'Címkék',
      'machine_name' => 'vocabulary_8',
      'description' => 'Szabadszavas címkék',
      'hierarchy' => 0,
      'module' => 'taxonomy',
      'weight' => 10,
    ),
  );
}
