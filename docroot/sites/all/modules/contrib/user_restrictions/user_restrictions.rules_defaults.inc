<?php

/**
 * @file
 * Default rules configuration for the User restrictions module.
 */

/**
 * Implements hook_default_rules_configuration().
 *
 * Provides a default rule to email the site email address
 * with details when a user attempts to log in or register
 * with restricted details.
 *
 * @return array 
 */
function user_restrictions_default_rules_configuration() {
  $rule = rules_reaction_rule();
  $rule->label = 'Email on User Restriction';
  $rule->active = FALSE;
  $rule->event('user_restrictions_denied')
    ->action('mail', array(
      'from' => '[site:mail]',
      'to' => '[site:mail]',
      'subject' => t('User Restrictions Denial'),
      'message' => t('User blocked on [form-type:value] form.
       Type of block: [type:value]
       Mask blocked: [mask:value]'),
      'format' => filter_default_format(),
    ));

  $configs['user_restrictions_email_on_denial'] = $rule;
  return $configs;
}
