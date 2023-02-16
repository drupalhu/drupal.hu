<?php
/**
 * @file
 * Hooks provided by the Mail Safety module.
 */

/**
 * Respond to a mail being inserted to the dashboard.
 * 
 * @param array $message
 *   The message array.
 *   
 * @return array
 *   Return the message array after any changes.
 */
function hook_mail_safety_pre_insert($message) {
  // Check to see if there are attachments.
  if (!empty($message['params']['attachments'])) {
    // Loop through the attachments and save the files.
    foreach ($message['params']['attachments'] as $key => $attachment) {
      $file = file_save_data($attachment['content'], 'public://' . time() . '-' . $attachment['filename']);
      $message['attachments'][$key] = $file;
    }
    // Remove the attachments from the e-mail.
    unset($message['params']['attachments']);
  }
  return $message;
}

/**
 * Respond to a mail being loaded.
 * 
 * @param array $message
 *   The message array.
 *   
 * @return array
 *   Return the message array after any changes.
 */
function hook_mail_safety_load($message) {
  if (!empty($message['attachments'])) {
    $message['has_attachments'] = TRUE;
  }
  return $message;
}

/**
 * Respond to a mail being deleted.
 *
 * @param int $mail_id
 *   The mail id as it is saved in the mail safety table.
 */
function hook_mail_safety_delete_mail($mail_id) {
  $mail = mail_safety_load($mail_id);
  if (empty($mail)) {
    return;
  }

  if (empty($mail['mail']['attachments'])) {
    return;
  }

  foreach ($mail['mail']['attachments'] as $file) {
    file_delete($file);
  }
}

/**
 * Respond to a mail before it is being send.
 * 
 * @param array $message
 *   The message array.
 *   
 * @return array
 *   Return the message array after any changes.
 */
function hook_mail_safety_pre_send($message) {
  // Loop through the attachments in a message.
  foreach ($message['attachments'] as $key => $attachment) {
    // Return the attachment to the e-mail to send it again.
    $message['params']['attachments'][$key] = array(
      'content' => file_get_contents($attachment->uri),
      'mime' => $attachment->filemime,
      'filename' => $attachment->filename,
    );
  }
  return $message;
}

/**
 * Alter the table structure of the mail safety dashboard.
 * 
 * @param array $table_structure
 *   The table structure that will be rendered as table.
 */
function hook_mail_safety_table_structure_alter($table_structure) {
  // Add a new column.
  $table_structure['header'][] = array(
    'data' => t('Files'),
  );

  // Loop through the mails to add the attachments in the table.
  foreach ($table_structure['rows'] as $mail_id => $row) {
    $mail = mail_safety_load($mail_id);

    if (!empty($mail['mail']['attachments'])) {
      $attachments = array();
      foreach ($mail['mail']['attachments'] as $attachment) {
        $attachments[] = array(
          '#theme' => 'file_link',
          '#file' => $attachment,
        );
      }
    }
    $table_structure['rows'][$mail_id]['data'][] = $attachments;
  }

  return $table_structure;
}
