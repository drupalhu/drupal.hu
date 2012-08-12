<?php
/**
 * @file
 * dhu_job.field_group.inc
 */

/**
 * Implements hook_field_group_info().
 */
function dhu_job_field_group_info() {
  $export = array();

  $field_group = new stdClass();
  $field_group->disabled = FALSE; /* Edit this to true to make a default field_group disabled initially */
  $field_group->api_version = 1;
  $field_group->identifier = 'group_job_announcer|node|job|form';
  $field_group->group_name = 'group_job_announcer';
  $field_group->entity_type = 'node';
  $field_group->bundle = 'job';
  $field_group->mode = 'form';
  $field_group->parent_name = '';
  $field_group->data = array(
    'label' => 'Ajánlatkiíró',
    'weight' => '3',
    'children' => array(
      0 => 'field_job_contact_email',
      1 => 'field_job_contact_person',
      2 => 'field_job_contact_phone',
      3 => 'field_job_organization',
    ),
    'format_type' => 'fieldset',
    'format_settings' => array(
      'label' => 'Ajánlatkiíró',
      'instance_settings' => array(
        'required_fields' => 0,
        'classes' => '',
        'description' => '',
      ),
      'formatter' => 'open',
    ),
  );
  $export['group_job_announcer|node|job|form'] = $field_group;

  $field_group = new stdClass();
  $field_group->disabled = FALSE; /* Edit this to true to make a default field_group disabled initially */
  $field_group->api_version = 1;
  $field_group->identifier = 'group_job_other_conditions|node|job|form';
  $field_group->group_name = 'group_job_other_conditions';
  $field_group->entity_type = 'node';
  $field_group->bundle = 'job';
  $field_group->mode = 'form';
  $field_group->parent_name = '';
  $field_group->data = array(
    'label' => 'Egyéb jellemzők és elvárások',
    'weight' => '2',
    'children' => array(
      0 => 'field_buzzwords',
      1 => 'field_job_experience',
      2 => 'field_job_other_language',
    ),
    'format_type' => 'fieldset',
    'format_settings' => array(
      'label' => 'Egyéb jellemzők és elvárások',
      'instance_settings' => array(
        'required_fields' => 0,
        'classes' => '',
        'description' => '',
      ),
      'formatter' => 'open',
    ),
  );
  $export['group_job_other_conditions|node|job|form'] = $field_group;

  $field_group = new stdClass();
  $field_group->disabled = FALSE; /* Edit this to true to make a default field_group disabled initially */
  $field_group->api_version = 1;
  $field_group->identifier = 'group_job_type|node|job|form';
  $field_group->group_name = 'group_job_type';
  $field_group->entity_type = 'node';
  $field_group->bundle = 'job';
  $field_group->mode = 'form';
  $field_group->parent_name = '';
  $field_group->data = array(
    'label' => '',
    'weight' => '1',
    'children' => array(
      0 => 'body',
      1 => 'field_job_advantage',
      2 => 'field_job_announcer_gives',
      3 => 'field_job_due_date',
      4 => 'field_job_expectations',
      5 => 'field_job_remotely',
      6 => 'field_job_timing',
      7 => 'field_place_of_work',
      8 => 'field_job_summary',
    ),
    'format_type' => 'fieldset',
    'format_settings' => array(
      'label' => '',
      'instance_settings' => array(
        'required_fields' => 0,
        'classes' => '',
        'description' => '',
      ),
      'formatter' => 'open',
    ),
  );
  $export['group_job_type|node|job|form'] = $field_group;

  return $export;
}
