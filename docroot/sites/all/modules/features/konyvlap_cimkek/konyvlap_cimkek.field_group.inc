<?php
/**
 * @file
 * konyvlap_cimkek.field_group.inc
 */

/**
 * Implements hook_field_group_info().
 */
function konyvlap_cimkek_field_group_info() {
  $field_groups = array();

  $field_group = new stdClass();
  $field_group->disabled = FALSE; /* Edit this to true to make a default field_group disabled initially */
  $field_group->api_version = 1;
  $field_group->identifier = 'group_book_vocs|node|book|form';
  $field_group->group_name = 'group_book_vocs';
  $field_group->entity_type = 'node';
  $field_group->bundle = 'book';
  $field_group->mode = 'form';
  $field_group->parent_name = '';
  $field_group->data = array(
    'label' => 'Szótárak',
    'weight' => '37',
    'children' => array(
      0 => 'taxonomy_vocabulary_10',
      1 => 'taxonomy_vocabulary_6',
      2 => 'field_voc_celkozonseg',
      3 => 'field_voc_szint',
    ),
    'format_type' => 'fieldset',
    'format_settings' => array(
      'formatter' => 'collapsible',
      'instance_settings' => array(
        'description' => '',
        'classes' => ' group-book-vocs field-group-fieldset',
        'required_fields' => 1,
      ),
    ),
  );
  $field_groups['group_book_vocs|node|book|form'] = $field_group;

  // Translatables
  // Included for use with string extractors like potx.
  t('Szótárak');

  return $field_groups;
}
