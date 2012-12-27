<?php
/**
 * @file
 * konyvlap_cimkek.features.taxonomy.inc
 */

/**
 * Implements hook_taxonomy_default_vocabularies().
 */
function konyvlap_cimkek_taxonomy_default_vocabularies() {
  return array(
    'celkozonseg' => array(
      'name' => 'Célközönség',
      'machine_name' => 'celkozonseg',
      'description' => 'Célközönség meghatározására használatos szótár.',
      'hierarchy' => '0',
      'module' => 'taxonomy',
      'weight' => '0',
    ),
    'szint' => array(
      'name' => 'Szint',
      'machine_name' => 'szint',
      'description' => 'A szótár felhasználó tudásszinteket jelenít meg. ',
      'hierarchy' => '0',
      'module' => 'taxonomy',
      'weight' => '0',
    ),
  );
}
