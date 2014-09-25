<?php
/**
 * @file
 * drupalhu_planet.features.taxonomy.inc
 */

/**
 * Implements hook_taxonomy_default_vocabularies().
 */
function drupalhu_planet_taxonomy_default_vocabularies() {
  return array(
    'planet' => array(
      'name' => 'Planet',
      'machine_name' => 'planet',
      'description' => 'The tags that are imported from the external websites via the Planet Drupal.hu feeds',
      'hierarchy' => '0',
      'module' => 'taxonomy',
      'weight' => '0',
    ),
    'vocabulary_12' => array(
      'name' => 'Hírcsatornák',
      'machine_name' => 'vocabulary_12',
      'description' => '',
      'hierarchy' => '1',
      'module' => 'taxonomy',
      'weight' => '0',
    ),
  );
}
