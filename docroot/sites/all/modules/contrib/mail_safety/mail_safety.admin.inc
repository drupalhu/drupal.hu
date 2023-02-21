<?php
/**
 * @file
 * Admin functionality for Mail Safety
 */

/**
 * Form constructor for the Mail Safety settings form.
 *
 * @ingroup forms
 */
function mail_safety_admin_settings_form() {
  $form = array();

  $form['mail_safety_enabled'] = array(
    '#title' => t('Stop outgoing mails'),
    '#type' => 'checkbox',
    '#description' => t('When Mail Safety is enabled it will stop all outgoing emails from being sent and will send them to either the dashboard and/or the defaut mail address instead.'),
    '#default_value' => variable_get('mail_safety_enabled', FALSE),
  );

  $form['mail_safety_send_mail_to_dashboard'] = array(
    '#title' => t('Send mail to dashboard'),
    '#type' => 'checkbox',
    '#description' => t('If enabled, all mails will be sent to the dashboard'),
    '#default_value' => variable_get('mail_safety_send_mail_to_dashboard', TRUE),
  );

  $form['mail_safety_send_mail_to_default_mail'] = array(
    '#title' => t('Send mail to default mail'),
    '#type' => 'checkbox',
    '#description' => t('If enabled, all mails will be sent to the the default mail address'),
    '#default_value' => variable_get('mail_safety_send_mail_to_default_mail', TRUE),
  );

  $form['mail_safety_default_mail_address'] = array(
    '#title' => t('Default mail address'),
    '#type' => 'textfield',
    '#description' => t('The default email address that outgoing e-mails will be rerouted to if enabled.'),
    '#default_value' => variable_get('mail_safety_default_mail_address', ''),
  );

  $form['mail_safety_mail_expire'] = array(
    '#title' => t('Remove stored mails older than the given age.'),
    '#type' => 'select',
    '#options' => array(0 => t('Never')) + drupal_map_assoc(array(3600, 86400, 604800, 1209600, 2419200), 'format_interval'),
    '#description' => t('Requires a correctly configured <a href="@cron">cron maintenance task</a>.', array('@cron' => url('admin/reports/status'))),
    '#default_value' => variable_get('mail_safety_mail_expire', 0),
  );

  return system_settings_form($form);
}

/**
 * Form constructor for the dashboard of Mail Safety.
 *
 * Shows an overview of all outgoing emails and let users debug or perform 
 * actions on them.
 * 
 * @ingroup forms
 */
function mail_safety_admin_dashboard_form() {
  $form = array();
  $table_structure = array();

  // Create the headers.
  $table_structure['header'] = array(
    array('data' => t('Subject')),
    array('data' => t('Date sent'), 'field' => 'sent', 'sort' => 'desc'),
    array('data' => t('To')),
    array('data' => t('CC')),
    array('data' => t('Module')),
    array('data' => t('Key')),
    array('data' => t('Details')),
    array('data' => t('Send to original')),
    array('data' => t('Send to default mail')),
    array('data' => t('Delete')),
  );

  // Create the query.
  $query = db_select('mail_safety_dashboard', 'msd')
  ->extend('PagerDefault')
  ->limit(50)
  ->extend('TableSort')
  ->orderByHeader($table_structure['header'])
  ->fields('msd', array(
    'mail_id',
    'sent',
    'mail',
  ));

  $results = $query->execute();

  // Fill the rows for the table.
  $table_structure['rows'] = array();
  foreach ($results as $row) {
    $mail = unserialize($row->mail);
    $table_structure['rows'][$row->mail_id] = array(
      'data' => array(
        l($mail['subject'], 'admin/config/development/mail_safety/' . $row->mail_id . '/view'),
        format_date($row->sent, 'short'),
        $mail['to'],
        (isset($mail['headers']['CC']) ? $mail['headers']['CC'] : t('none')),
        $mail['module'],
        $mail['key'],
        l(t('Details'), 'admin/config/development/mail_safety/' . $row->mail_id . '/details'),
        l(t('Send to original'), 'admin/config/development/mail_safety/' . $row->mail_id . '/send_original'),
        l(t('Send to default mail'), 'admin/config/development/mail_safety/' . $row->mail_id . '/send_default'),
        l(t('Delete'), 'admin/config/development/mail_safety/' . $row->mail_id . '/delete'),
      ),
    );
  }

  // Let other modules change the table structure to add or remove
  // information to be shown. E.g. attachments that need to be downloaded.
  drupal_alter('mail_safety_table_structure', $table_structure);

  $form['mails']['table'] = array(
    '#theme' => 'table',
    '#header' => $table_structure['header'],
    '#rows' => $table_structure['rows'],
    '#caption' => 'Mail Safety Dashboard',
    '#sticky' => TRUE,
    '#empty' => t('No mails found'),
  );

  $form['mails']['pager'] = array(
    '#theme' => 'pager',
    '#tags' => array(),
  );

  return $form;
}

/**
 * Page callback: Displays a mail filtered by the dashboard.
 *
 * @param array $mail
 *   The mail array filtered by Mail Safety
 * 
 * @see node_menu()
 */
function mail_safety_admin_view_mail($mail) {
  $system = drupal_mail_system($mail['mail']['module'], $mail['mail']['key']);
  $mail['mail'] = $system->format($mail['mail']);
  return theme('mail_safety_mail', array('mail' => $mail));
}

/**
 * Page callback: Displays a mail its parameters filtered by the dashboard.
 *
 * @param array $mail
 *   The mail array filtered by Mail Safety
 *   
 * @see node_menu()
 */
function mail_safety_admin_details_mail($mail) {
  return theme('mail_safety_details', array('mail' => $mail));
}

/**
 * Form constructor for the send to original mail address form.
 * 
 * @see mail_safety_admin_send_original_form()
 *
 * @ingroup forms
 */
function mail_safety_admin_send_original_form($form, $form_state, $mail) {
  $form = array();

  $form['mail_id'] = array(
    '#type' => 'value',
    '#value' => $mail['mail_id'],
  );

  $form['#submit'] = array('mail_safety_admin_send_original_form_submit');

  return confirm_form(
    $form,
    t('Are you sure you want to send "@subject" to @to?', array(
      '@subject' => $mail['mail']['subject'],
      '@to' => $mail['mail']['to'],
      )
    ),
    'admin/config/development/mail_safety',
    t('This will send the mail to the original recipient.'),
    t('Send')
  );
}

/**
 * Form submission handler for mail_safety_admin_send_original_form.
 * 
 * Sends the mail to the original recipient.
 */
function mail_safety_admin_send_original_form_submit(&$form, &$form_state) {
  if (!empty($form_state['values']['mail_id']) && $mail = mail_safety_load($form_state['values']['mail_id'])) {

    // Resend the mail and bypass mail_alter by using the drupal_mail_system
    $mail_array = $mail['mail'];
    $mail_array['send'] = TRUE;

    // Let other modules respond before a mail is sent.
    // E.g. add attachments that were saved in the mail.
    $modules = module_implements('mail_safety_pre_send');

    foreach ($modules as $module) {
      $mail_array = module_invoke($module, 'mail_safety_pre_send', $mail_array);
    }

    $system = drupal_mail_system($mail_array['module'], $mail_array['key']);
    $mail_array = $system->format($mail_array);
    $mail_array['result'] = $system->mail($mail_array);

    if ($mail_array['result']) {
      drupal_set_message(t('Succesfully sent the message to @to', array('@to' => $mail_array['to'])));
    }
    else {
      drupal_set_message(t('Failed to send the message to @to', array('@to' => $mail_array['to'])), 'error');
    }
  }
  else {
    drupal_set_message(t('Could not send the mail to the original sender'), 'error');
  }

  $form_state['redirect'] = 'admin/config/development/mail_safety';
}

/**
 * Form constructor for the send to default mail address form.
 * 
 * @see mail_safety_admin_send_default_form()
 *
 * @ingroup forms
 */
function mail_safety_admin_send_default_form($form, $form_state, $mail) {
  $form = array();

  $form['to'] = array(
    '#type' => 'textfield',
    '#title' => t('Send to another address'),
    '#description' => t('Only use this field if you want the e-mail to go to an address other than the default address.'),
    '#default_value' => variable_get('mail_safety_default_mail_address', ''),
  );

  $form['mail_id'] = array(
    '#type' => 'value',
    '#value' => $mail['mail_id'],
  );

  $form['#submit'] = array('mail_safety_admin_send_default_form_submit');

  return confirm_form(
    $form,
    t('Are you sure you want to send "@subject" to @to?', array(
      '@subject' => $mail['mail']['subject'],
      '@to' => variable_get('mail_safety_default_mail_address', ''),
      )
    ),
    'admin/config/development/mail_safety',
    t('This will send the mail to the default e-mail address.'),
    t('Send')
  );
}

/**
 * Form submission handler for mail_safety_admin_send_default_form_submit.
 * 
 * Sends the mail to a default recipient.
 */
function mail_safety_admin_send_default_form_submit(&$form, &$form_state) {
  if (!empty($form_state['values']['mail_id']) && !empty($form_state['values']['to']) && $mail = mail_safety_load($form_state['values']['mail_id'])) {

    // Resend the mail and bypass mail_alter by using
    // the drupal_mail_system.
    $mail_array = $mail['mail'];
    $mail_array['send'] = TRUE;

    // Let other modules respond before a mail is sent.
    // E.g. add attachments that were saved in the mail.
    $modules = module_implements('mail_safety_pre_send');

    foreach ($modules as $module) {
      $mail_array = module_invoke($module, 'mail_safety_pre_send', $mail_array);
    }

    // Change the recipient to the default recipient.
    $mail_array['to'] = $form_state['values']['to'];

    // Remove the CC recipients.
    unset($mail_array['headers']['CC']);

    $system = drupal_mail_system($mail_array['module'], $mail_array['key']);
    $mail_array = $system->format($mail_array);
    $mail_array['result'] = $system->mail($mail_array);

    if ($mail_array['result']) {
      drupal_set_message(t('Succesfully sent the message to @to', array('@to' => $mail_array['to'])));
    }
    else {
      drupal_set_message(t('Failed to send the message to @to', array('@to' => $mail_array['to'])), 'error');
    }
  }
  else {
    drupal_set_message(t('Could not send the mail to the original sender'), 'error');
  }

  $form_state['redirect'] = 'admin/config/development/mail_safety';
}

/**
 * Form constructor to delete a mail from the dashboard.
 * 
 * @see mail_safety_admin_delete_form_submit()
 *
 * @ingroup forms
 */
function mail_safety_admin_delete_form($form, $form_state, $mail) {
  $form = array();

  $form['mail_id'] = array(
    '#type' => 'value',
    '#value' => $mail['mail_id'],
  );

  $form['#submit'] = array('mail_safety_admin_delete_form_submit');

  return confirm_form(
    $form,
    t('Are you sure you want to delete "@subject"?', array('@subject' => $mail['mail']['subject'])),
    'admin/config/development/mail_safety',
    t('This will remove the mail from the dashboard.'),
    t('Delete')
  );
}

/**
 * Form submission handler for mail_safety_admin_delete_form_submit.
 * 
 * Delete a mail from the dashboard.
 */
function mail_safety_admin_delete_form_submit(&$form, &$form_state) {
  if (!empty($form_state['values']['mail_id']) && mail_safety_delete_mail($form_state['values']['mail_id'])) {
    drupal_set_message(t('Succesfully deleted the mail'));
  }
  else {
    drupal_set_message(t('Could not delete the mail'), 'error');
  }

  $form_state['redirect'] = 'admin/config/development/mail_safety';
}
