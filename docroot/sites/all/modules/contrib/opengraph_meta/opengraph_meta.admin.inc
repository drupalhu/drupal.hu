<?php

require_once('opengraph_meta.common.inc');


/**
 * Menu callback: settings form.
 */
function opengraph_meta_settings_form() {

  $types = OpenGraphMetaDrupalLayer::get_node_types();

  $type_options = array();
  foreach ($types as $id => $data) {
    $type_options[$id] = $data->name;
  }

  $form[OPENGRAPH_META_VAR_CONTENT_TYPES_ENABLED] = array(
    '#title' => t('Enable for the following content types'),
    '#type' => 'checkboxes',
    '#options' => $type_options,
    '#description' => t('The content types for which to enable Open Graph meta tags. If none are selected then tags will be enabled for ALL content types.'),
    '#default_value' => variable_get(OPENGRAPH_META_VAR_CONTENT_TYPES_ENABLED, array()),
  );

  $form['defaults'] = array(
    '#type' => 'fieldset',
    '#title' => 'Default values for meta tags',
  );
  $form['defaults'][OPENGRAPH_META_VAR_SITE_NAME] = array(
    '#title' => t('Site name'),
    '#type' => 'textfield',
    '#description' => t('The value for the %site_name meta tag.',array('%site_name' => 'og:site_name')),
    '#default_value' => variable_get(OPENGRAPH_META_VAR_SITE_NAME, variable_get('site_name','Drupal')),
  );
  $form['defaults'][OPENGRAPH_META_VAR_FALLBACK_IMG] = array(
    '#title' => t('Fallback image'),
    '#type' => 'textfield',
    '#description' => t('Absolute or site-relative URL to an image to use for the %tag tag for nodes which don\'t have their own images.',array('%tag' => 'og:image')),
    '#default_value' => variable_get(OPENGRAPH_META_VAR_FALLBACK_IMG, ''),
  );
  $form['defaults']['types'] = array(
    '#type' => 'fieldset',
    '#title' => t('Mapping content type to meta type'),
    '#description' => t('These can be overridden on a per-node basis.'),
    '#tree' => TRUE,
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  foreach ($types as $id => $data) {
    $form['defaults']['types'][OPENGRAPH_META_VAR_CONTENT_TYPE_.$id] = array(
      '#title' => $data->name,
      '#type' => 'select',
      '#options' => OpenGraphMeta::instance()->get_all_og_types_for_select_field(),
      '#description' => t('The value for the %ogtype meta tag for nodes of type %type. If left unset then the %ogtype tag will not get output for this content type.',array('%ogtype' => 'og:type', '%type' => $id)),
      '#default_value' => variable_get(OPENGRAPH_META_VAR_CONTENT_TYPE_.$id, ''),
    );
  }

  // For Drupal 6, if CCK text fields enabled
  if (6 === OPENGRAPH_META_DRUPAL_VERSION && module_exists('text')) {
    $form['defaults']['description_cck'] = array(
      '#type' => 'fieldset',
      '#title' => t('How to generate og:description for each content type'),
      '#description' => t('By default a node\'s %field field is used to auto-generate the value of the %tag tag. Here you can opt for a different CCK text field to be used instead. Note that only content types with extra CCK text fields will be shown here.',array('%field' => 'body', '%tag' => 'og:description')),
      '#tree' => TRUE,
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $content_type_info = _content_type_info();
    foreach ($content_type_info['content types'] as $ct_id => $ct_data) {
      // if we have field info for this content type
      if (isset($content_type_info['content types'][$ct_id]['fields'])) {
        $fields = $content_type_info['content types'][$ct_id]['fields'];
        $options = array();
        foreach ($fields as $field_name => $field_info) {
          if ('text' == $field_info['widget']['module'])
            $options[$field_name] = $field_info['widget']['label'];
        }
        if (!empty($options)) {
          $options = array('' => '') + $options;
          $form['defaults']['description_cck'][OPENGRAPH_META_VAR_CONTENT_TYPE_CCK_.$ct_id] = array(
            '#title' => $ct_data['name'],
            '#type' => 'select',
            '#options' => $options,
            '#description' => t('The value for the %tag tag for nodes of type %type. If left unset then the %default field will be used.',array('%tag' => 'og:description', '%type' => $ct_data['name'], '%default' => 'body')),
            '#default_value' => variable_get(OPENGRAPH_META_VAR_CONTENT_TYPE_CCK_.$ct_id, ''),
          );
        }
      }
    }
  }

  // optional tags defaults
  $optionals = new stdClass();
  $optionals->opengraph_meta = variable_get(OPENGRAPH_META_VAR_OPTIONAL_TAGS, array());

  // Location stuff
  $form['defaults']['location'] = array(
    '#type' => 'fieldset',
    '#title' => t('Location tags'),
    '#description' => t('These can be overridden on a per-node basis.'),
    '#tree' => TRUE,
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#weight' => 100,
  );
  $form['defaults']['location'] = array_merge($form['defaults']['location'], _opengraph_meta_location_form_fields($optionals));

  // Contact stuff
  $form['defaults']['contact'] = array(
    '#type' => 'fieldset',
    '#title' => t('Contact tags'),
    '#description' => t('These can be overridden on a per-node basis.'),
    '#tree' => TRUE,
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#weight' => 100,
  );
  $form['defaults']['contact'] = array_merge($form['defaults']['contact'], _opengraph_meta_contact_form_fields($optionals));

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Save',
  );

  return $form;
}


function opengraph_meta_settings_form_submit($form, &$form_state) {

  $values = $form_state['values'];

  // save content types enabled
  variable_set(OPENGRAPH_META_VAR_CONTENT_TYPES_ENABLED, $values[OPENGRAPH_META_VAR_CONTENT_TYPES_ENABLED]);

  // save og:type for each content type
  $types = OpenGraphMetaDrupalLayer::get_node_types();
  foreach ($types as $id => $data) {
    OpenGraphMetaDrupalLayer::update_default_ogtype_for_node_type($id, $values);
  }

  // save other vars
  variable_set(OPENGRAPH_META_VAR_SITE_NAME, $values[OPENGRAPH_META_VAR_SITE_NAME]);
  variable_set(OPENGRAPH_META_VAR_FALLBACK_IMG, $values[OPENGRAPH_META_VAR_FALLBACK_IMG]);

  // save optional tags
  $options = array();
  foreach (_opengraph_meta_location_form_fields() as $f => $info) {
    $options[$f] = $values['location'][$f];
  }
  foreach (_opengraph_meta_contact_form_fields() as $f => $info) {
    $options[$f] = $values['contact'][$f];
  }
  variable_set(OPENGRAPH_META_VAR_OPTIONAL_TAGS, $options);

  // success
  drupal_set_message("All Open Graph Meta Tags options have been updated");
}



