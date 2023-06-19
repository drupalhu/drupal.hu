<?php

/**
 * @file
 * drupalhu_ureg.features.user_permission.inc
 */

/**
 * Implements hook_user_default_permissions().
 */
function drupalhu_ureg_user_default_permissions() {
  $permissions = array();

  // Exported permission: 'skip CAPTCHA'.
  $permissions['skip CAPTCHA'] = array(
    'name' => 'skip CAPTCHA',
    'roles' => array(
      'administrator' => 'administrator',
      'authenticated user' => 'authenticated user',
    ),
    'module' => 'captcha',
  );

  return $permissions;
}
