<?php

/**
 * @file
 * Administration pages for the user restrictions module.
 */

/**
 * Form builder for user restriction delete form.
 *
 * @see user_restrictions_ui_delete_rule_form_submit()
 * @ingroup forms
 */
function user_restrictions_ui_delete_rule_form($form, &$form_state, $rule) {
  $access_types = $rule->getTypeOptions();
  $form = array('#rule' => serialize($rule));

  $output = confirm_form($form,
    t(
      'Are you sure you want to delete the @type rule for %mask?',
      array('@type' => $access_types[$rule->type], '%mask' => $rule->mask)
    ),
    'admin/config/people/user-restrictions',
    t('This action cannot be undone.'),
    t('Delete'),
    t('Cancel')
  );

  return $output;
}

/**
 * Form submission handler for user_restrictions_ui_delete_rule_form().
 */
function user_restrictions_ui_delete_rule_form_submit($form, &$form_state) {
  $rule = unserialize($form['#rule']);
  $rule->delete();

  drupal_set_message(t('The access rule has been deleted.'));
  $form_state['redirect'] = 'admin/config/people/user-restrictions';
}

/**
 * Form generator for the user restriction edit form.
 *
 * @see user_restrictions_ui_edit_rule_form_validate()
 * @see user_restrictions_ui_edit_rule_form_submit()
 * @ingroup forms
 */
function user_restrictions_ui_edit_rule_form($form, &$form_state, $rule = NULL) {
  if (!isset($rule)) {
    $rule = UserRestrictions::getInstance($rule);
  }

  $form['#rule'] = serialize($rule);

  $form['status'] = array(
    '#type' => 'radios',
    '#title' => t('Access type'),
    '#default_value' => $rule->status,
    '#options' => $rule->getStatusOptions(),
  );

  $form['type'] = array(
    '#type' => 'radios',
    '#title' => t('Rule type'),
    '#default_value' => $rule->type ? $rule->type : 'mail',
    '#options' => $rule->getTypeOptions(),
  );

  $form['mask'] = array(
    '#type' => 'textfield',
    '#title' => t('Mask'),
    '#size' => 30,
    '#maxlength' => 64,
    '#default_value' => $rule->mask,
    '#description' => t('%ampersand: Matches any number of characters, even zero characters.<br />%underscore: Matches exactly one character.', array('%ampersand' => '%', '%underscore' => '_')),
    '#required' => TRUE,
  );
  
  $expire_options = array(0 => t('Never'));
  if ($rule->expire) {
    $expire_options['-1'] = format_interval($rule->expire - REQUEST_TIME);
    $default_expire = '-1';
  }
  else {
    $default_expire = 0;
  }
  $form['expire'] = array(
    '#type' => 'select',
    '#title' => t('Expire'),
    '#default_value' => $default_expire,
    '#options' => $expire_options + drupal_map_assoc(array(3600, 10800, 21600, 32400, 43200, 86400, 172800, 259200, 604800, 1209600, 2419200, 4838400, 9676800), 'format_interval'),
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save rule'),
  );

  return $form;
}

/**
 * Form validation handler for user_restrictions_ui_edit_rule_form().
 */
function user_restrictions_ui_edit_rule_form_validate($form, &$form_state) {
  if (!trim($form_state['values']['mask'])) {
    form_set_error('mask', t('The mask value contains only spaces, or other not printable characters'));
    return;
  }

  // Verify if there are other rules of the same type with the same mask.
  $rule = unserialize($form['#rule']);
  if ($rule->exists($form_state['values']) && !$rule->urid) {
    form_set_error('mask', t('The mask value has been already used in a rule.'));
  }  
}

/**
 * Form submission handler for user_restrictions_ui_edit_rule_form().
 */
function user_restrictions_ui_edit_rule_form_submit($form, &$form_state) {
  $rule = unserialize($form['#rule']);

  //set the values from the form into the object
  $rule->status = $form_state['values']['status'];
  $rule->type = $form_state['values']['type'];
  $rule->mask = $form_state['values']['mask'];
  if ($form_state['values']['expire'] != -1) {
    $rule->expire = $form_state['values']['expire'] ? $form_state['values']['expire'] + REQUEST_TIME : 0;
  }
  $rule->save();

  drupal_set_message(t('The access rule has been saved.'));
}

/**
 * Form builder for the list of user restrictions.
 *
 * @see user_restrictions_ui_check_email_validate()
 * @see user_restrictions_ui_check_email_submit()
 * @see user_restrictions_ui_check_username_submit()
 * @ingroup forms
 */
function user_restrictions_ui_overview_form($form, &$form_state) {
  $destination = drupal_get_destination();
  $header = array(
    'status' => array('data' => t('Access type'), 'field' => 'status', 'sort' => 'desc'),
    'type' => array('data' => t('Rule type'), 'field' => 'type'),
    'mask' => array('data' => t('Mask'), 'field' => 'mask'),
    'expire' => array('data' => t('Expire'), 'field' => 'expire'),
    'operations' => array('data' => t('Operations'), 'colspan' => 2)
  );
  $rows = UserRestrictions::getRestrictionTable($header, $destination);

  $form['rules'] = array(
    '#theme' => 'table',
    '#header' => $header,
    '#rows' => $rows,
    '#empty' => t('There are currently no user restrictions.'),
  );


  // Show the fieldset only if there are restriction rules.
  if (count($rows)) {
    $form['check_rules'] = array(
      '#type' => 'fieldset',
      '#title' => t('Check rules'),
      '#attributes' => array('class' => array('container-inline')),
    );

    $form['check_rules']['value'] = array(
      '#type' => 'textfield',
      '#size' => 30,
      '#maxlength' => USERNAME_MAX_LENGTH,
    );

    $form['check_rules']['check_username'] = array(
      '#type' => 'submit',
      '#value' => t('Check username'),
      '#submit' => array('user_restrictions_ui_check_username_submit'),
    );

    $form['check_rules']['check_email'] = array(
      '#type' => 'submit',
      '#value' => t('Check e-mail'),
      '#submit' => array('user_restrictions_ui_check_email_submit'),
      '#validate' => array('user_restrictions_ui_check_email_validate'),
    );
  }

  $form['pager'] = array('#markup' => theme('pager', array('tags' => NULL)));

  return $form;
}

/**
 * Form builder to import access rules from the D6 access table.
 *
 * @see user_restrictions_ui_import_rule_form_submit()
 * @ingroup forms
 */
function user_restrictions_ui_import_rule_form($form, &$form_state) {
  $form = array();

  if (db_table_exists('access')) {
    $count = db_query("SELECT COUNT(DISTINCT aid) FROM {access} WHERE type <> 'host'")->fetchField();
  }

  if (isset($count) && $count > 0) {
    $form['access_import'] = array(
      '#type' => 'submit',
      '#value' => t('Import'),
    );
    $instructions = t(':count items to be imported', array(':count' => $count));
  }
  else {
    $instructions = t('No Access Rules to import');
  }

  $form['import_help'] = array(
    '#type' => 'item',
    '#markup' => $instructions,
    '#weight' => -10,
  );

  return $form;
}

/**
 * Form submission function.
 *
 * @see user_restrictions_ui_import_rule_form()
 * @ingroup forms
 */
function user_restrictions_ui_import_rule_form_submit($form, &$form_state) {
  $batch = array(
    'title' => t('Importing'),
    'operations' => array(
      array('user_restrictions_ui_import', array()),
    ),
    'file' => drupal_get_path('module', 'user_restrictions_ui') . '/user_restrictions_ui.admin.inc',
    'finished' => 'user_restrictions_ui_import_finished',
  );
  batch_set($batch);

}

/**
 * Batch import function to import access rules from the access table
 *
 * @see user_restrictions_ui_import_rule_form_submit()
 * @param $context
 */
function user_restrictions_ui_import(&$context) {
  if (empty($context['sandbox']) && db_table_exists('access')) {
    $context['sandbox'] = array();
    $context['sandbox']['progress'] = 0;
    $context['sandbox']['current_aid'] = 0;
    $context['sandbox']['max'] = db_query("SELECT COUNT(DISTINCT aid) FROM {access} WHERE type <> 'host'")->fetchField();
  }

  $limit = 10;
  if (!empty($context['sandbox']['max'])) {
    $rules = db_select('access', 'a')
      ->fields('a')
      ->condition('aid', $context['sandbox']['current_aid'], '>')
      ->condition('type', 'host', '!=')
      ->orderBy('aid', 'ASC')
      ->range(0, $limit)
      ->execute();
    foreach ($rules as $rule) {
      // Access rules uses 'user' whilst user_restrictions uses 'name'
      $rule->type = $rule->type == 'user' ? 'name' : $rule->type;
      db_merge('user_restrictions')
        ->key(array(
          'type' => $rule->type,
          'mask' => $rule->mask,
          'status' => $rule->status,
        ))
        ->fields(array('status' => $rule->status))
        ->execute();
      $context['results'][] = check_plain($rule->mask);
      $context['sandbox']['progress']++;
      $context['sandbox']['current_aid'] = $rule->aid;
    }
  }
  $context['finished'] = (
    empty($context['sandbox']['max']) ? 1 : ($context['sandbox']['progress'] / $context['sandbox']['max'])
  );

}

/**
 * Batch finished function.
 *
 * @see user_restrictions_ui_import_rule_form_submit()
 * @param $success
 * @param $results
 * @param $operations
 */
function user_restrictions_ui_import_finished($success, $results, $operations) {
  if ($success) {
    $message = format_plural(count($results), 'One record imported.', '@count records imported.');
    $status = 'status';
  }
  else {
    $message = t('Error while importing.');
    $status = 'error';
  }

  drupal_set_message($message, $status);
}

/**
 * Form validation handler for user_restrictions_ui_overview_form().
 *
 * @see user_restrictions_ui_overview_form()
 * @see user_restrictions_ui_check_email_submit()
 */
function user_restrictions_ui_check_email_validate($form, &$form_state) {
  if (empty($form_state['values']['value'])
      || !valid_email_address($form_state['values']['value'])) {
    form_set_error('value', t('The entered value is not a valid email address.'));
  }
}

/**
 * Form submission handler for user_restrictions_ui_overview_form().
 *
 * @see user_restrictions_ui_overview_form()
 * @see user_restrictions_ui_check_email_validate()
 */
function user_restrictions_ui_check_email_submit($form, &$form_state) {
  $error = UserRestrictions::check($form_state, 'check_mail');
  if ($error['message'] != '') {
    drupal_set_message($error['message'], 'warning');
  }
  else {
    drupal_set_message(
      t('The e-mail address %mail is allowed.', array('%mail' => $form_state['values']['value']))
    );
  }
}

/**
 * Form submission handler for user_restrictions_ui_overview_form().
 *
 * @see user_restrictions_ui_overview_form()
 */
function user_restrictions_ui_check_username_submit($form, &$form_state) {
  $error = UserRestrictions::check($form_state, 'check_name');
  if ($error['message'] != '') {
    drupal_set_message($error['message'], 'warning');
  }
  else {
    drupal_set_message(
      t('The username %name is allowed.', array('%name' => $form_state['values']['value']))
    );
  }
}
