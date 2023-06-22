<?php

/**
 * @file
 * Rules_info for the User restrictions module.
 */

/**
 * Implements hook_rules_event_info
 *
 * Whenever a user is denied registration or login this rules event
 * will trigger allowing actions to occur.
 *
 * Because rules 7.x-2.2 does not support the passing of non-entities
 * as variables in the 'direct input' token selector the requirement is either
 * using rules 7.x-2.3 / dev or applying the patch here:
 * http://drupal.org/node/812058#comment-6536478.
 *
 * @return array
 */
function user_restrictions_rules_event_info() {
  return array(
    'user_restrictions_denied' => array(
      'group' => t('User Restrictions'),
      'label' => t('After a user is denied registration.'),
      'module' => 'user_restrictions',
      'variables' => array(
        'type' => array('type' => 'text', 'label' => t('Type of User Restrictions match.')),
        'mask' => array('type' => 'text',  'label' => t('Mask blocked by User Restrictions.')),
        'form_type' => array('type' => 'text',  'label' => t('Which form the denial occured on.')),
      ),
    ),
  );
}

/**
 * Implements hook_rules_action_info
 *
 * Provides actions for rules events to block and
 * unblock names/emails.
 * 
 * @return array
 */
function user_restrictions_rules_action_info() {
  $defaults = array(
    'group' => t('User Restrictions'),
    'parameter' => array(
      'account' => array(
        'label' => t('User'),
        'type' => 'user',
        'description' => t('The User the event will affect.'),
      ),
    ),
  );

  $actions['user_restrictions_block_name'] = $defaults + array(
    'label' => t('Block username'),
  );
  $actions['user_restrictions_block_mail'] = $defaults + array(
    'label' => t('Block email address'),
  );
  $actions['user_restrictions_unblock_name'] = $defaults + array(
    'label' => t('Unblock username'),
  );
  $actions['user_restrictions_unblock_mail'] = $defaults + array(
    'label' => t('Unblock email address'),
  );

  return $actions;
}

/**
 * Rules callback to block a username.
 * 
 * @param object $user
 */
function user_restrictions_block_name($user) {
  $instance = UserRestrictions::getInstance();
  $instance->type = 'name';
  $instance->mask = $user->name;
  $instance->save();
}

/**
 * Rules callback to unblock a username.
 *
 * @param object $user
 */
function user_restrictions_unblock_name($user) {
  $instance = UserRestrictions::getInstanceByMask('name', $user->name);
  $instance->delete();
}
/**
 * Rules callback to block an email.
 *
 * @param object $user
 */
function user_restrictions_block_mail($user) {
  $instance = UserRestrictions::getInstance();
  $instance->type = 'mail';
  $instance->mask = $user->mail;
  $instance->save();
}
/**
 * Rules callback to unblock an email.
 *
 * @param object $user
 */
function user_restrictions_unblock_mail($user) {
  $instance = UserRestrictions::getInstanceByMask('mail', $user->mail);
  $instance->delete();
}
