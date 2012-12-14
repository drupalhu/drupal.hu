<?php
/**
 * @file
 * about_page.field_group.inc
 */

/**
 * Implements hook_field_group_info().
 */
function about_page_field_group_info() {
  $export = array();

  $field_group = new stdClass();
  $field_group->disabled = FALSE; /* Edit this to true to make a default field_group disabled initially */
  $field_group->api_version = 1;
  $field_group->identifier = 'group_book_infos|node|book|form';
  $field_group->group_name = 'group_book_infos';
  $field_group->entity_type = 'node';
  $field_group->bundle = 'book';
  $field_group->mode = 'form';
  $field_group->parent_name = '';
  $field_group->data = array(
    'label' => 'Könyvlap adatok',
    'weight' => '1',
    'children' => array(
      0 => 'taxonomy_vocabulary_10',
      1 => 'taxonomy_vocabulary_6',
      2 => 'field_book_audience',
      3 => 'field_book_level',
    ),
    'format_type' => 'fieldset',
    'format_settings' => array(
      'formatter' => 'collapsible',
      'instance_settings' => array(
        'description' => '',
        'classes' => '',
        'required_fields' => 1,
      ),
    ),
  );
  $export['group_book_infos|node|book|form'] = $field_group;

  return $export;
}
