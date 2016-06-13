<?php
/**
 * @file
 * drupalhu_slideshow.features.field_instance.inc
 */

/**
 * Implements hook_field_default_field_instances().
 */
function drupalhu_slideshow_field_default_field_instances() {
  $field_instances = array();

  // Exported field_instance: 'node-slideshow_item-field_slideshow_description'.
  $field_instances['node-slideshow_item-field_slideshow_description'] = array(
    'bundle' => 'slideshow_item',
    'default_value' => NULL,
    'deleted' => 0,
    'description' => 'Its visible in slideshow.',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'module' => 'text',
        'settings' => array(),
        'type' => 'text_default',
        'weight' => 1,
      ),
      'teaser' => array(
        'label' => 'above',
        'settings' => array(),
        'type' => 'hidden',
        'weight' => 0,
      ),
    ),
    'entity_type' => 'node',
    'field_name' => 'field_slideshow_description',
    'label' => 'Leírás',
    'required' => 0,
    'settings' => array(
      'text_processing' => 0,
      'user_register_form' => FALSE,
    ),
    'widget' => array(
      'active' => 1,
      'module' => 'text',
      'settings' => array(
        'size' => 60,
      ),
      'type' => 'text_textfield',
      'weight' => 32,
    ),
  );

  // Exported field_instance: 'node-slideshow_item-field_slideshow_image'.
  $field_instances['node-slideshow_item-field_slideshow_image'] = array(
    'bundle' => 'slideshow_item',
    'deleted' => 0,
    'description' => 'Image show in slideshow.',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'module' => 'image',
        'settings' => array(
          'image_link' => '',
          'image_style' => 'thumbnail',
        ),
        'type' => 'image',
        'weight' => 0,
      ),
      'teaser' => array(
        'label' => 'above',
        'settings' => array(),
        'type' => 'hidden',
        'weight' => 0,
      ),
    ),
    'entity_type' => 'node',
    'field_name' => 'field_slideshow_image',
    'label' => 'Kép',
    'required' => 1,
    'settings' => array(
      'alt_field' => 0,
      'default_image' => 0,
      'file_directory' => 'slideshow_image',
      'file_extensions' => 'png gif jpg jpeg',
      'max_filesize' => '',
      'max_resolution' => '',
      'min_resolution' => '960x300',
      'title_field' => 0,
      'user_register_form' => FALSE,
    ),
    'widget' => array(
      'active' => 1,
      'module' => 'image',
      'settings' => array(
        'preview_image_style' => 'thumbnail',
        'progress_indicator' => 'throbber',
      ),
      'type' => 'image_image',
      'weight' => 31,
    ),
  );

  // Exported field_instance: 'node-slideshow_item-field_slideshow_url'.
  $field_instances['node-slideshow_item-field_slideshow_url'] = array(
    'bundle' => 'slideshow_item',
    'default_value' => NULL,
    'deleted' => 0,
    'description' => 'If you need reference this field, paste your url.',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'module' => 'link',
        'settings' => array(),
        'type' => 'link_default',
        'weight' => 2,
      ),
      'teaser' => array(
        'label' => 'above',
        'settings' => array(),
        'type' => 'hidden',
        'weight' => 0,
      ),
    ),
    'entity_type' => 'node',
    'field_name' => 'field_slideshow_url',
    'label' => 'Webcím',
    'required' => 0,
    'settings' => array(
      'absolute_url' => 1,
      'attributes' => array(
        'class' => '',
        'configurable_title' => 0,
        'rel' => '',
        'target' => 'default',
        'title' => '',
      ),
      'display' => array(
        'url_cutoff' => 80,
      ),
      'enable_tokens' => 1,
      'title' => 'none',
      'title_label_use_field_label' => FALSE,
      'title_maxlength' => 255,
      'title_value' => '',
      'url' => 0,
      'user_register_form' => FALSE,
      'validate_url' => 1,
    ),
    'widget' => array(
      'active' => 0,
      'module' => 'link',
      'settings' => array(),
      'type' => 'link_field',
      'weight' => 33,
    ),
  );

  // Translatables
  // Included for use with string extractors like potx.
  t('If you need reference this field, paste your url.');
  t('Image show in slideshow.');
  t('Its visible in slideshow.');
  t('Kép');
  t('Leírás');
  t('Webcím');

  return $field_instances;
}
